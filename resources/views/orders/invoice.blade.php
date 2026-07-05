<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #FAC-{{ $order->id }}</title>
    
    <!-- Figtree Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite or Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white;
                color: black;
                font-size: 12px;
            }
            .print-border-none {
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 p-4 sm:p-8 min-h-screen flex flex-col justify-between">

    <!-- Top Action bar (Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto w-full mb-6 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Retour aux détails
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0a2.25 2.25 0 11-4.5 0m-3.6 0a2.25 2.25 0 11-4.5 0m3.36 0h10.08m-11.25-2.25h12.0m-11.25-2.25h12.0M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            Imprimer / Enregistrer PDF
        </button>
    </div>

    <!-- Invoice Sheet -->
    <div class="max-w-4xl mx-auto w-full bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 print-border-none shadow-sm flex-1 flex flex-col justify-between">
        <div>
            <!-- Header: Logo & Invoice details -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 border-b border-slate-100 pb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="bg-slate-900 text-white rounded-lg px-2.5 py-1 text-xl font-black">L</span>
                        Laravel Store
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">E-commerce de confiance</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">FACTURE</span>
                    <h2 class="text-lg font-bold text-slate-900 mt-2">#FAC-{{ $order->id }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Date d'émission : {{ $order->order_date->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Client & Company Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 border-b border-slate-100 text-sm">
                <div>
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs mb-3">Émetteur</h3>
                    <p class="font-semibold text-slate-800">Laravel Store SAS</p>
                    <p class="text-slate-500 mt-1">123 Boulevard du Dev</p>
                    <p class="text-slate-500">75001 Paris, France</p>
                    <p class="text-slate-500 mt-1.5">contact@laravelstore.com</p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs mb-3">Facturé à</h3>
                    <p class="font-semibold text-slate-800">{{ $order->user->name }}</p>
                    <p class="text-slate-500 mt-1">Client #{{ $order->user->id }}</p>
                    <p class="text-slate-500 break-all">{{ $order->user->email }}</p>
                    <p class="text-slate-500 mt-2 font-medium">Statut de paiement : 
                        <span class="font-semibold {{ $order->status === 'cancelled' ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $order->status === 'cancelled' ? 'Annulé' : 'Payé' }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="py-8">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs mb-4">Détail des articles</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 font-semibold">
                                <th class="py-3">Désignation</th>
                                <th class="py-3 text-right">Prix unitaire</th>
                                <th class="py-3 text-center">Quantité</th>
                                <th class="py-3 text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $rawSubtotal = 0.00;
                            @endphp
                            @foreach($order->orderItems as $item)
                                @php
                                    $rawSubtotal += $item->subtotal;
                                @endphp
                                <tr class="text-slate-800">
                                    <td class="py-4 font-medium">{{ $item->product->name }}</td>
                                    <td class="py-4 text-right">{{ number_format($item->unit_price, 2) }} EUR</td>
                                    <td class="py-4 text-center">{{ $item->quantity }}</td>
                                    <td class="py-4 text-right font-medium">{{ number_format($item->subtotal, 2) }} EUR</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Totals & Footnotes -->
        <div class="border-t border-slate-100 pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-8">
                <!-- Payment notes -->
                <div class="text-xs text-slate-400 max-w-sm">
                    <p class="font-semibold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Informations</p>
                    <p class="leading-relaxed">Le paiement a été traité en ligne de manière sécurisée. Cette facture sert de justificatif d'achat. Pour toute question, veuillez contacter notre support.</p>
                </div>
                <!-- Breakdown -->
                <div class="w-full sm:w-80 text-sm space-y-2.5">
                    <div class="flex justify-between text-slate-500 font-medium">
                        <span>Sous-total</span>
                        <span>{{ number_format($rawSubtotal, 2) }} EUR</span>
                    </div>
                    
                    @if($order->coupon)
                        <div class="flex justify-between text-emerald-700 font-medium">
                            <span class="flex items-center gap-1.5">
                                Réduction (Code: <span class="uppercase font-bold">{{ $order->coupon->code }}</span>)
                            </span>
                            <span>-{{ number_format($order->discount_amount, 2) }} EUR</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-900 border-t border-slate-200 pt-3 text-lg font-bold">
                        <span>Total Payé</span>
                        <span>{{ number_format($order->total_amount, 2) }} EUR</span>
                    </div>
                </div>
            </div>

            <!-- Footer signature / thanks -->
            <div class="text-center text-xs text-slate-400 border-t border-slate-100 mt-12 pt-6">
                <p class="font-medium text-slate-500">Merci de votre confiance !</p>
                <p class="mt-1">Laravel Store SAS — Siret 123 456 789 00010 — R.C.S. Paris</p>
            </div>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Un court délai pour laisser au rendu le temps de s'installer proprement
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
