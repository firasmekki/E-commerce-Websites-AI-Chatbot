const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

const BASE_URL = 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, 'docs', 'screenshots');
const WAIT = (ms) => new Promise((r) => setTimeout(r, ms));

const ADMIN = { email: 'admin@nextcommerce.test', password: 'password' };
const CLIENT = { email: 'client@nextcommerce.test', password: 'password' };

async function hideLoaders(page) {
    await page.evaluate(() => {
        document.querySelectorAll('.spinner, .loading, [data-loading], .skeleton').forEach((el) => {
            el.style.display = 'none';
        });
    });
}

async function capture(page, filename, label) {
    await WAIT(500);
    await hideLoaders(page);
    await page.screenshot({ path: path.join(OUT, filename), fullPage: true });
    console.log(`  ok ${label} -> ${filename}`);
}

async function login(page, { email, password }) {
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await page.$eval('#email', (el) => (el.value = ''));
    await page.$eval('#password', (el) => (el.value = ''));
    await page.type('#email', email, { delay: 15 });
    await page.type('#password', password, { delay: 15 });
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('[type=submit]'),
    ]);
}

async function newTab(browser) {
    const context = await browser.createBrowserContext();
    const page = await context.newPage();
    await page.setViewport({ width: 1440, height: 900 });
    return page;
}

(async () => {
    if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });

    const browser = await puppeteer.launch({
        headless: true,
        protocolTimeout: 120000,
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu', '--disable-dev-shm-usage'],
    });

    console.log('\n-- Interface Client --');
    const cli = await newTab(browser);

    await cli.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_login.png', 'Connexion');

    await cli.goto(`${BASE_URL}/register`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_register.png', 'Inscription');

    await login(cli, CLIENT);
    await capture(cli, 'localhost_8000_client_dashboard.png', 'Dashboard client');

    await cli.goto(`${BASE_URL}/products`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_products.png', 'Boutique');

    await cli.goto(`${BASE_URL}/products/2`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_product-detail.png', 'Detail produit');

    await cli.goto(`${BASE_URL}/categories`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_categories.png', 'Categories');

    await cli.goto(`${BASE_URL}/categories/1`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_category-detail.png', 'Detail categorie');

    // Add the product to the cart so the cart page has content
    await cli.goto(`${BASE_URL}/products/2`, { waitUntil: 'networkidle0' });
    const addToCartBtn = await cli.$('form[action*="/cart/"] button[type=submit]');
    if (addToCartBtn) {
        await Promise.all([
            cli.waitForNavigation({ waitUntil: 'networkidle0' }).catch(() => {}),
            addToCartBtn.click(),
        ]);
    }
    await cli.goto(`${BASE_URL}/cart`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_cart.png', 'Panier');

    await cli.goto(`${BASE_URL}/orders`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_orders.png', 'Mes commandes');

    await cli.goto(`${BASE_URL}/orders/1`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_order-detail.png', 'Detail commande');

    await cli.goto(`${BASE_URL}/orders/1/invoice`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_order-invoice.png', 'Facture');

    await cli.goto(`${BASE_URL}/profile`, { waitUntil: 'networkidle0' });
    await capture(cli, 'localhost_8000_client_profile.png', 'Profil');

    await cli.goto(`${BASE_URL}/dashboard`, { waitUntil: 'networkidle0' });
    const chatBtn = await cli.$('#chatbot-launcher');
    if (chatBtn) {
        await chatBtn.click();
        await WAIT(400);
    }
    await capture(cli, 'localhost_8000_client_chatbot.png', 'Assistant IA');

    await cli.close();

    console.log('\n-- Interface Administrateur --');
    const adm = await newTab(browser);
    await login(adm, ADMIN);

    await capture(adm, 'localhost_8000_admin_dashboard.png', 'Dashboard admin');

    await adm.goto(`${BASE_URL}/admin/products`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_products.png', 'Produits');

    await adm.goto(`${BASE_URL}/admin/products/create`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_products-create.png', 'Creer produit');

    await adm.goto(`${BASE_URL}/admin/products/2/edit`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_products-edit.png', 'Modifier produit');

    await adm.goto(`${BASE_URL}/admin/categories`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_categories.png', 'Categories');

    await adm.goto(`${BASE_URL}/admin/customers`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_customers.png', 'Comptes clients');

    await adm.goto(`${BASE_URL}/admin/orders`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_orders.png', 'Commandes');

    await adm.goto(`${BASE_URL}/admin/coupons`, { waitUntil: 'networkidle0' });
    await capture(adm, 'localhost_8000_admin_coupons.png', 'Coupons');

    await adm.close();
    await browser.close();

    const total = fs.readdirSync(OUT).filter((f) => f.endsWith('.png')).length;
    console.log(`\n${total} captures saved in ${OUT}`);
})().catch((err) => {
    console.error('Error:', err);
    process.exit(1);
});
