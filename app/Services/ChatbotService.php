<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatHistory;
use App\Models\Product;
use App\Models\Order;
use App\Services\ProductService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private ProductService $productService;
    private OrderService $orderService;

    public function __construct(
        ProductService $productService,
        OrderService $orderService
    ) {
        $this->productService = $productService;
        $this->orderService = $orderService;
    }

    public function processMessage(User $user, string $message): string
    {
        try {
            // Get conversation history for context
            $conversationHistory = $this->getConversationHistory($user);
            
            // Retrieve relevant business data based on the message
            $businessData = $this->retrieveBusinessData($message, $user);
            
            if (empty(config('services.gemini.api_key'))) {
                $response = $this->buildLocalResponse($message, $businessData);
            } else {
                // Build intelligent prompt
                $prompt = $this->buildPrompt($message, $businessData, $conversationHistory);

                // Call AI API
                $response = $this->callAIAPI($prompt);
            }
            
            // Save conversation to history
            $this->saveConversation($user, $message, $response, $businessData);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            throw new \Exception('Erreur lors du traitement du message.');
        }
    }

    private function getConversationHistory(User $user): array
    {
        return ChatHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($chat) => [
                'user' => $chat->user_message,
                'bot' => $chat->bot_response,
            ])
            ->reverse()
            ->toArray();
    }

    private function retrieveBusinessData(string $message, User $user): array
    {
        $data = [];
        $lowerMessage = strtolower($message);

        // Profile-related queries
        if (str_contains($lowerMessage, 'profil') || str_contains($lowerMessage, 'profile') || 
            str_contains($lowerMessage, 'compte') || str_contains($lowerMessage, 'qui suis-je') || 
            str_contains($lowerMessage, 'me connecter') || str_contains($lowerMessage, 'user') || 
            str_contains($lowerMessage, 'utilisateur')) {
            $data['user_profile'] = [
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'status' => $user->status,
                'created_at' => $user->created_at->format('d/m/Y'),
            ];
        }

        // Cart-related queries
        if (str_contains($lowerMessage, 'panier') || str_contains($lowerMessage, 'caddie') || 
            str_contains($lowerMessage, 'achat') || str_contains($lowerMessage, 'mon panier')) {
            $cart = session()->get('cart', []);
            $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
            $cartItems = [];
            $total = 0;
            foreach ($cart as $productId => $item) {
                $product = $products->get((int) $productId);
                if ($product) {
                    $subtotal = $item['quantity'] * $product->price;
                    $total += $subtotal;
                    $cartItems[] = [
                        'name' => $product->name,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                }
            }
            $data['cart'] = [
                'items' => $cartItems,
                'total' => $total,
            ];
        }

        // Product-related queries
        if (str_contains($lowerMessage, 'produit') || str_contains($lowerMessage, 'product') || 
            str_contains($lowerMessage, 'article') || str_contains($lowerMessage, 'catalogue') || 
            str_contains($lowerMessage, 'boutique') || str_contains($lowerMessage, 'magasin') ||
            str_contains($lowerMessage, 'liste des produits')) {
            
            if (str_contains($lowerMessage, 'stock') || str_contains($lowerMessage, 'disponible') || 
                str_contains($lowerMessage, 'quantité') || str_contains($lowerMessage, 'rupture')) {
                $data['in_stock_products'] = $this->productService->getInStockProducts()->toArray();
                $data['out_of_stock_products'] = Product::where('stock', '<=', 0)->get()->toArray();
            } elseif (str_contains($lowerMessage, 'catégorie') || str_contains($lowerMessage, 'category') || 
                      str_contains($lowerMessage, 'categories') || str_contains($lowerMessage, 'rayon')) {
                $data['products_by_category'] = Product::with('category')->get()->groupBy('category.name')->toArray();
            } else {
                $data['all_products'] = $this->productService->getAllProducts()->toArray();
            }
        }

        // Categories list query (standalone)
        if (str_contains($lowerMessage, 'catégorie') || str_contains($lowerMessage, 'category') || 
            str_contains($lowerMessage, 'categories') || str_contains($lowerMessage, 'rayon')) {
            $data['categories'] = \App\Models\Category::withCount('products')->get()->toArray();
        }

        // Order-related queries
        if (str_contains($lowerMessage, 'commande') || str_contains($lowerMessage, 'order') || 
            str_contains($lowerMessage, 'achats') || str_contains($lowerMessage, 'mes commandes')) {
            $data['user_orders'] = $this->orderService->getOrdersByUser($user->id)->toArray();
        }

        // Statistics queries
        if (str_contains($lowerMessage, 'statistique') || str_contains($lowerMessage, 'statistic') || 
            str_contains($lowerMessage, 'chiffre') || str_contains($lowerMessage, 'revenu') || 
            str_contains($lowerMessage, 'stats') || str_contains($lowerMessage, 'dashboard')) {
            $data['product_stats'] = $this->productService->getProductStatistics();
            $data['order_stats'] = $this->orderService->getOrderStatistics();
        }

        // Top selling products
        if (str_contains($lowerMessage, 'vendu') || str_contains($lowerMessage, 'popular') || 
            str_contains($lowerMessage, 'top') || str_contains($lowerMessage, 'meilleur')) {
            $data['top_products'] = $this->productService->getTopSellingProducts()->toArray();
        }

        // Search queries
        if (str_contains($lowerMessage, 'recherche') || str_contains($lowerMessage, 'search') || 
            str_contains($lowerMessage, 'trouve') || str_contains($lowerMessage, 'cherche')) {
            $searchTerms = $this->extractSearchTerms($message);
            if (!empty($searchTerms)) {
                $data['search_results'] = $this->productService->searchProducts($searchTerms)->toArray();
            }
        }

        // Review/ratings queries
        if (str_contains($lowerMessage, 'avis') || str_contains($lowerMessage, 'commentaire') || 
            str_contains($lowerMessage, 'note') || str_contains($lowerMessage, 'review') || 
            str_contains($lowerMessage, 'opinion')) {
            $searchTerms = $this->extractSearchTerms($message);
            if (!empty($searchTerms)) {
                $products = Product::where('name', 'like', "%{$searchTerms}%")
                    ->with(['reviews.user'])
                    ->take(3)
                    ->get();
                
                $reviewData = [];
                foreach ($products as $p) {
                    $reviews = $p->reviews->take(5)->map(fn($r) => [
                        'user' => $r->user?->name ?? 'Anonyme',
                        'rating' => $r->rating,
                        'comment' => $r->comment,
                    ])->toArray();
                    
                    $reviewData[] = [
                        'product_name' => $p->name,
                        'average_rating' => (float) ($p->reviews()->avg('rating') ?? 0),
                        'reviews_count' => $p->reviews()->count(),
                        'reviews' => $reviews,
                    ];
                }
                $data['product_reviews'] = $reviewData;
            }
        }

        // Promotions/coupons queries
        if (str_contains($lowerMessage, 'promo') || str_contains($lowerMessage, 'réduction') || 
            str_contains($lowerMessage, 'reduction') || str_contains($lowerMessage, 'coupon') || 
            str_contains($lowerMessage, 'remise') || str_contains($lowerMessage, 'code')) {
            $data['active_coupons'] = \App\Models\Coupon::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->get(['code', 'type', 'value', 'expires_at'])
                ->toArray();
        }

        return $data;
    }

    private function extractSearchTerms(string $message): string
    {
        // Simple extraction - remove common words
        $message = strtolower($message);
        $stopWords = [
            'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'pour', 'avec', 'sur', 'dans', 
            'recherche', 'search', 'trouve', 'cherche', 'moi', 's\'il vous plaît', 'svp',
            'avis', 'commentaire', 'commentaires', 'note', 'notes', 'review', 'reviews', 'opinion'
        ];
        
        foreach ($stopWords as $word) {
            // Use word boundaries or simple spaces to avoid partial matches
            $message = preg_replace('/\b' . preg_quote($word, '/') . '\b/u', '', $message);
        }
        
        // Replace multiple spaces with a single space
        $message = preg_replace('/\s+/', ' ', $message);
        
        return trim($message);
    }

    private function buildPrompt(string $userMessage, array $businessData, array $conversationHistory): string
    {
        $prompt = "Tu es un assistant intelligent pour une plateforme e-commerce. ";
        $prompt .= "Tu dois répondre aux questions des utilisateurs en utilisant les données métier fournies. ";
        $prompt .= "Réponds en français de manière professionnelle et helpful.\n\n";
        
        // Add conversation context
        if (!empty($conversationHistory)) {
            $prompt .= "Historique de la conversation récente:\n";
            foreach ($conversationHistory as $exchange) {
                $prompt .= "- Utilisateur: {$exchange['user']}\n";
                $prompt .= "- Assistant: {$exchange['bot']}\n";
            }
            $prompt .= "\n";
        }
        
        // Add business data
        if (!empty($businessData)) {
            $prompt .= "Données métier disponibles:\n";
            $prompt .= json_encode($businessData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prompt .= "\n\n";
        }
        
        $prompt .= "Question de l'utilisateur: {$userMessage}\n\n";
        $prompt .= "Instructions:\n";
        $prompt .= "- Utilise les données fournies pour répondre de manière précise\n";
        $prompt .= "- Si les données ne suffisent pas, indique-le poliment\n";
        $prompt .= "- Sois concis et direct dans tes réponses\n";
        $prompt .= "- Pour les questions statistiques, fournis des chiffres précis\n";
        $prompt .= "- Pour les recommandations, base-toi sur les données disponibles\n";
        
        return $prompt;
    }

    private function buildLocalResponse(string $message, array $businessData): string
    {
        $lowerMessage = strtolower($message);

        // 1. HELP / ASSISTANCE / CONVERSATION STANDARD
        if (str_contains($lowerMessage, 'aide') || str_contains($lowerMessage, 'help') || 
            str_contains($lowerMessage, 'option') || str_contains($lowerMessage, 'que faire') || 
            str_contains($lowerMessage, 'fonctionnalité') || str_contains($lowerMessage, 'comment')) {
            return "💡 **Comment puis-je vous aider sur NextCommerce ?**\n\n"
                . "Voici les commandes ou sujets que vous pouvez explorer :\n"
                . "🔹 **\"les produits\"** : Afficher les produits disponibles dans la boutique\n"
                . "🔹 **\"catégories\"** : Voir les différents rayons et thématiques\n"
                . "🔹 **\"stock\"** : Connaître l'état du stock et les disponibilités\n"
                . "🔹 **\"recherche [nom]\"** : Chercher un produit par son nom (ex: *recherche laptop*)\n"
                . "🔹 **\"avis [nom]\"** : Consulter les avis d'un produit (ex: *avis iphone*)\n"
                . "🔹 **\"promo\"** : Découvrir nos codes promos et réductions actives\n"
                . "🔹 **\"profil\"** : Consulter vos informations de compte\n"
                . "🔹 **\"panier\"** : Voir le contenu de votre panier actuel\n"
                . "🔹 **\"commandes\"** : Suivre vos commandes récentes et leur statut\n"
                . "🔹 **\"statistiques\"** : Résumé des ventes, revenus et du catalogue\n\n"
                . "N'hésitez pas à me poser une question directement !";
        }

        if (str_contains($lowerMessage, 'bonjour') || str_contains($lowerMessage, 'salut') || 
            str_contains($lowerMessage, 'cc') || str_contains($lowerMessage, 'hello') || 
            str_contains($lowerMessage, 'hey') || str_contains($lowerMessage, 'bonsoir')) {
            return "👋 **Bonjour ! Bienvenue sur l'assistant NextCommerce.**\n\n"
                . "Je suis à votre disposition pour vous renseigner sur notre catalogue de produits, vos commandes en cours, votre panier ou les statistiques de la boutique.\n\n"
                . "👉 Tapez **\"aide\"** à tout moment pour voir ce que je peux faire !";
        }

        // 1.5 REVIEWS AND RATINGS
        if (isset($businessData['product_reviews'])) {
            $reviews = $businessData['product_reviews'];
            if (empty($reviews)) {
                return "📝 **Avis produits :**\n\nAucun avis trouvé pour les produits correspondants.";
            }

            $reply = "📝 **Avis et notes de nos produits :**\n\n";
            foreach ($reviews as $pr) {
                $rating = number_format($pr['average_rating'], 1);
                $reply .= "⭐️ **{$pr['product_name']}** (Note : {$rating}/5 sur {$pr['reviews_count']} avis)\n";
                if (empty($pr['reviews'])) {
                    $reply .= "  ↳ _Aucun commentaire n'a encore été publié pour ce produit._\n";
                } else {
                    foreach ($pr['reviews'] as $rev) {
                        $reply .= "  ↳ *{$rev['user']}* (Note : {$rev['rating']}/5) : \"{$rev['comment']}\"\n";
                    }
                }
                $reply .= "\n";
            }
            return trim($reply);
        }

        // 1.6 COUPONS AND PROMOTIONS
        if (isset($businessData['active_coupons'])) {
            $coupons = $businessData['active_coupons'];
            if (empty($coupons)) {
                return "🎟️ **Codes promos :**\n\nAucun code promotionnel n'est actif pour le moment. Repassez plus tard !";
            }

            $reply = "🎟️ **Voici nos codes promotionnels actifs :**\n\n";
            foreach ($coupons as $c) {
                $val = $c['type'] === 'percent' ? number_format($c['value'], 0) . '%' : number_format($c['value'], 2) . ' EUR';
                $expiry = $c['expires_at'] ? " (expire le " . date('d/m/Y', strtotime($c['expires_at'])) . ")" : "";
                $reply .= "• Code **{$c['code']}** : **-{$val}** de réduction{$expiry}\n";
            }
            $reply .= "\n👉 Vous pouvez appliquer ces codes directement dans votre [panier](/cart).";
            return $reply;
        }

        // 2. PROFILE / MON COMPTE
        if (isset($businessData['user_profile'])) {
            $prof = $businessData['user_profile'];
            $role = $prof['is_admin'] ? "🔴 Administrateur" : "🟢 Client standard";
            return "👤 **Votre profil utilisateur :**\n\n"
                . "• **Nom** : {$prof['name']}\n"
                . "• **Email** : {$prof['email']}\n"
                . "• **Rôle** : {$role}\n"
                . "• **Statut du compte** : " . ucfirst($prof['status']) . "\n"
                . "• **Membre depuis le** : {$prof['created_at']}\n\n"
                . "Vous pouvez modifier ces informations depuis votre espace profil.";
        }

        // 3. PANIER / CART
        if (isset($businessData['cart'])) {
            $cart = $businessData['cart'];
            if (empty($cart['items'])) {
                return "🛒 **Votre panier est actuellement vide.**\n\n"
                    . "Parcourez notre catalogue et ajoutez des produits pour commencer vos achats !";
            }

            $itemsList = "";
            foreach ($cart['items'] as $item) {
                $itemsList .= "• **{$item['name']}** x{$item['quantity']} : " . number_format($item['subtotal'], 2) . " EUR\n";
            }

            return "🛒 **Contenu de votre panier :**\n\n"
                . $itemsList . "\n"
                . "💰 **Total du panier** : **" . number_format($cart['total'], 2) . " EUR**\n\n"
                . "👉 Vous pouvez finaliser votre commande en vous rendant sur la page de votre [panier](/cart).";
        }

        // 4. TOP PRODUCTS
        if (isset($businessData['top_products'])) {
            $top = collect($businessData['top_products'])->take(5);
            if ($top->isEmpty()) {
                return "🔥 **Produits populaires :**\n\n"
                    . "Aucune vente n'a encore été enregistrée pour le moment. Soyez le premier à commander !";
            }

            $items = $top
                ->map(fn($p, $idx) => ($idx + 1) . ". 🏆 **{$p['name']}** : {$p['price']} EUR (vendu {$p['total_sold']} fois)")
                ->implode("\n");

            return "🔥 **Nos produits les plus vendus :**\n\n" . $items;
        }

        // 5. STOCKS / STOCK / DISPONIBILITÉS
        if (str_contains($lowerMessage, 'stock') || str_contains($lowerMessage, 'disponible') || 
            str_contains($lowerMessage, 'quantité') || str_contains($lowerMessage, 'rupture')) {
            
            $reply = "📦 **État de nos stocks et disponibilités :**\n\n";
            
            $inStock = collect($businessData['in_stock_products'] ?? [])->take(5);
            if ($inStock->isEmpty()) {
                $reply .= "⚠️ Aucun produit n'est actuellement disponible en stock.\n";
            } else {
                $reply .= "🟢 **En stock (Top 5) :**\n";
                foreach ($inStock as $p) {
                    $reply .= "• **{$p['name']}** : " . number_format($p['price'], 2) . " EUR (stock: {$p['stock']})\n";
                }
            }

            $outStock = collect($businessData['out_of_stock_products'] ?? [])->take(5);
            if (!$outStock->isEmpty()) {
                $reply .= "\n🔴 **En rupture de stock :**\n";
                foreach ($outStock as $p) {
                    $reply .= "• **{$p['name']}** (" . number_format($p['price'], 2) . " EUR)\n";
                }
            }

            return $reply;
        }

        // 6. CATEGORIES / RAYONS
        if (isset($businessData['categories']) && (str_contains($lowerMessage, 'catégorie') || 
            str_contains($lowerMessage, 'category') || str_contains($lowerMessage, 'categories') || 
            str_contains($lowerMessage, 'rayon'))) {
            
            $cats = collect($businessData['categories']);
            if ($cats->isEmpty()) {
                return "📂 Aucun rayon ou catégorie n'est défini pour le moment.";
            }

            $list = "📂 **Nos rayons et catégories :**\n\n";
            foreach ($cats as $cat) {
                $list .= "• **{$cat['name']}** ({$cat['products_count']} produits)\n";
                if (!empty($cat['description'])) {
                    $list .= "  _\"{$cat['description']}\"_\n";
                }
            }

            return $list;
        }

        // 7. SEARCH RESULTS
        if (isset($businessData['search_results'])) {
            $products = collect($businessData['search_results'])->take(5);

            if ($products->isEmpty()) {
                return "🔍 **Recherche :**\n\n"
                    . "Désolé, aucun produit ne correspond à votre recherche. Essayez d'autres mots-clés !";
            }

            $items = $products
                ->map(fn($product) => "• **{$product['name']}** : " . number_format($product['price'], 2) . " EUR (stock: {$product['stock']})")
                ->implode("\n");

            return "🔍 **Résultats de recherche trouvés :**\n\n{$items}";
        }

        // 8. ALL PRODUCTS
        if (isset($businessData['all_products'])) {
            $products = collect($businessData['all_products'])->take(5);
            if ($products->isEmpty()) {
                return "🛍️ **Catalogue produits :**\n\n"
                    . "Aucun produit n'est encore enregistré dans notre catalogue. L'administrateur les ajoutera très bientôt !";
            }

            $items = $products
                ->map(fn($product) => "• **{$product['name']}** : " . number_format($product['price'], 2) . " EUR (stock: {$product['stock']})")
                ->implode("\n");

            return "🛍️ **Sélection de nos produits :**\n\n{$items}\n\n👉 Retrouvez l'intégralité de nos articles sur notre [boutique](/products).";
        }

        // 9. ORDERS / COMMANDES
        if (isset($businessData['user_orders'])) {
            $orders = collect($businessData['user_orders'])->take(5);

            if ($orders->isEmpty()) {
                return "📦 **Vos commandes :**\n\n"
                    . "Vous n'avez pas encore passé de commande sur notre boutique.";
            }

            $statusEmojis = [
                'pending' => '⏳ En attente',
                'processing' => '⚙️ En préparation',
                'shipped' => '🚚 Expédiée',
                'delivered' => '✅ Livrée',
                'cancelled' => '❌ Annulée',
            ];

            $items = $orders
                ->map(function($order) use ($statusEmojis) {
                    $status = $statusEmojis[$order['status']] ?? $order['status'];
                    $date = date('d/m/Y', strtotime($order['order_date']));
                    return "• **Commande #{$order['id']}** ({$date}) : **" . number_format($order['total_amount'], 2) . " EUR**\n"
                         . "  ↳ Statut : {$status}";
                })
                ->implode("\n\n");

            return "📦 **Vos dernières commandes (Top 5) :**\n\n{$items}\n\n👉 Suivez tous vos achats détaillés dans votre espace [mes commandes](/orders).";
        }

        // 10. STATISTICS / REVENUS
        if (isset($businessData['product_stats']) || isset($businessData['order_stats'])) {
            $productStats = $businessData['product_stats'] ?? [];
            $orderStats = $businessData['order_stats'] ?? [];

            $totalProducts = $productStats['total_products'] ?? 0;
            $inStock = $productStats['in_stock'] ?? 0;
            $totalOrders = $orderStats['total_orders'] ?? 0;
            $revenue = number_format($orderStats['total_revenue'] ?? 0, 2);

            return "📊 **Tableau de bord et statistiques NextCommerce :**\n\n"
                . "🛍️ **Catalogue** :\n"
                . "• Nombre total de produits : **{$totalProducts}**\n"
                . "• Produits en stock : **{$inStock}**\n\n"
                . "📈 **Ventes et Activité** :\n"
                . "• Nombre total de commandes : **{$totalOrders}**\n"
                . "• Chiffre d'affaires global : **{$revenue} EUR**\n\n"
                . "💡 _Ces statistiques sont calculées en temps réel à partir des données de l'application._";
        }

        // 11. FALLBACK CONVERSATION
        return "🤖 **Je suis à votre écoute !**\n\n"
            . "Je peux vous donner des détails sur nos produits, catégories, stocks, votre panier ou vos commandes.\n\n"
            . "👉 Tapez **\"aide\"** pour voir toutes les rubriques que je prends en charge.";
    }

    private function callAIAPI(string $prompt): string
    {
        $apiKey = config('services.gemini.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception('Clé API Gemini non configurée');
        }

        try {
            $response = Http::timeout(30)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]
            );

            if ($response->failed()) {
                throw new \Exception('API call failed: ' . $response->body());
            }

            $data = $response->json();
            
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                throw new \Exception('Invalid API response structure');
            }

            return $data['candidates'][0]['content']['parts'][0]['text'];
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            throw new \Exception('Erreur de communication avec l\'API IA');
        }
    }

    private function saveConversation(User $user, string $userMessage, string $botResponse, array $businessData): void
    {
        ChatHistory::create([
            'user_id' => $user->id,
            'user_message' => $userMessage,
            'bot_response' => $botResponse,
            'conversation_context' => $businessData,
        ]);
    }
}
