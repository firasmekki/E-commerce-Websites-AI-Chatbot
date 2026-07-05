const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

// ─── Configuration ────────────────────────────────────────────────────────────
// Si l'app tourne via XAMPP :      http://localhost/web2026pfa/public
// Si l'app tourne via artisan :    http://localhost:8000
const BASE_URL = 'http://localhost/web2026pfa/public';

const OUT = path.join(__dirname, 'screenshots_rapport');
const WAIT = (ms) => new Promise((r) => setTimeout(r, ms));

const ADMIN  = { email: 'admin@nextcommerce.test',  password: 'password' };
const CLIENT = { email: 'client@nextcommerce.test', password: 'password' };
// ──────────────────────────────────────────────────────────────────────────────

async function capture(page, filename, label) {
    await WAIT(600);
    await page.screenshot({ path: path.join(OUT, filename), fullPage: true });
    console.log(`  ✓  ${label}`);
}

async function login(page, { email, password }) {
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await page.$eval('#email',    el => el.value = '');
    await page.$eval('#password', el => el.value = '');
    await page.type('#email',    email,    { delay: 20 });
    await page.type('#password', password, { delay: 20 });
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('[type=submit]'),
    ]);
}

async function newTab(browser) {
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });
    return page;
}

(async () => {
    if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });

    console.log('\n📸  Démarrage des captures...\n');

    const browser = await puppeteer.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    // ── 1. Pages publiques (visiteur non connecté) ──────────────────────────
    console.log('── Pages publiques ──');
    const pub = await newTab(browser);

    await pub.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await capture(pub, '01_connexion.png', 'Page Connexion');

    await pub.goto(`${BASE_URL}/register`, { waitUntil: 'networkidle0' });
    await capture(pub, '02_inscription.png', "Page Inscription");

    await pub.goto(`${BASE_URL}/products`, { waitUntil: 'networkidle0' });
    await capture(pub, '03_boutique_visiteur.png', 'Boutique (visiteur)');

    await pub.goto(`${BASE_URL}/categories`, { waitUntil: 'networkidle0' });
    await capture(pub, '04_categories_visiteur.png', 'Catégories (visiteur)');

    // Détail d'un produit public
    const firstLink = await pub.$('a[href*="/products/"]');
    if (firstLink) {
        await Promise.all([
            pub.waitForNavigation({ waitUntil: 'networkidle0' }),
            firstLink.click(),
        ]);
        await capture(pub, '05_detail_produit.png', 'Détail produit');
    }

    await pub.close();

    // ── 2. Session Client ────────────────────────────────────────────────────
    console.log('\n── Espace client ──');
    const cli = await newTab(browser);
    await login(cli, CLIENT);

    await capture(cli, '06_dashboard_client.png', 'Dashboard client');

    await cli.goto(`${BASE_URL}/products`, { waitUntil: 'networkidle0' });
    await capture(cli, '07_boutique_client.png', 'Boutique (connecté)');

    await cli.goto(`${BASE_URL}/cart`, { waitUntil: 'networkidle0' });
    await capture(cli, '08_panier.png', 'Panier');

    await cli.goto(`${BASE_URL}/orders`, { waitUntil: 'networkidle0' });
    await capture(cli, '09_commandes_client.png', 'Mes commandes');

    await cli.goto(`${BASE_URL}/profile`, { waitUntil: 'networkidle0' });
    await capture(cli, '10_profil.png', 'Profil');

    await cli.close();

    // ── 3. Session Admin ─────────────────────────────────────────────────────
    console.log('\n── Espace admin ──');
    const adm = await newTab(browser);
    await login(adm, ADMIN);

    await capture(adm, '11_dashboard_admin.png', 'Dashboard admin');

    await adm.goto(`${BASE_URL}/admin/products`, { waitUntil: 'networkidle0' });
    await capture(adm, '12_admin_produits.png', 'Admin – Liste produits');

    await adm.goto(`${BASE_URL}/admin/products/create`, { waitUntil: 'networkidle0' });
    await capture(adm, '13_admin_creer_produit.png', 'Admin – Créer produit');

    // Modifier le premier produit existant
    await adm.goto(`${BASE_URL}/admin/products`, { waitUntil: 'networkidle0' });
    const editBtn = await adm.$('a[href*="/admin/products/"][href$="/edit"]');
    if (editBtn) {
        await Promise.all([
            adm.waitForNavigation({ waitUntil: 'networkidle0' }),
            editBtn.click(),
        ]);
        await capture(adm, '14_admin_modifier_produit.png', 'Admin – Modifier produit');
    }

    await adm.goto(`${BASE_URL}/admin/categories`, { waitUntil: 'networkidle0' });
    await capture(adm, '15_admin_categories.png', 'Admin – Catégories');

    await adm.goto(`${BASE_URL}/admin/customers`, { waitUntil: 'networkidle0' });
    await capture(adm, '16_admin_clients.png', 'Admin – Clients');

    await adm.goto(`${BASE_URL}/admin/orders`, { waitUntil: 'networkidle0' });
    await capture(adm, '17_admin_commandes.png', 'Admin – Commandes');

    await adm.goto(`${BASE_URL}/admin/coupons`, { waitUntil: 'networkidle0' });
    await capture(adm, '18_admin_coupons.png', 'Admin – Coupons');

    await adm.close();
    await browser.close();

    const total = fs.readdirSync(OUT).length;
    console.log(`\n✅  ${total} captures sauvegardées dans :\n   ${OUT}\n`);
})().catch((err) => {
    console.error('\n❌ Erreur :', err.message);
    process.exit(1);
});
