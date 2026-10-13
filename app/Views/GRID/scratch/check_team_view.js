const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });

    console.log('Navigating to /index.php/grid/team...');
    await page.goto('http://127.0.0.1/index.php/grid/team', { waitUntil: 'networkidle0' });
    await new Promise(r => setTimeout(r, 2000));

    await page.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/brain/0faf4638-1358-4965-a11e-91ae516bd355/scratch/current_team_welcome.png' });
    console.log('Screenshot 1 captured: current_team_welcome.png');

    // Click to launch workspace if button exists
    const launchBtn = await page.$('div.cursor-pointer, button');
    // Let's find the workspace card to launch
    const wsCards = await page.$$('main div.cursor-pointer');
    if (wsCards.length > 0) {
        console.log('Clicking workspace card to open main workspace view...');
        await wsCards[0].click();
        await new Promise(r => setTimeout(r, 2000));
        await page.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/brain/0faf4638-1358-4965-a11e-91ae516bd355/scratch/current_team_workspace.png' });
        console.log('Screenshot 2 captured: current_team_workspace.png');

        // Also click a channel to see chat stream
        const channelBtn = await page.$('aside button');
        // Let's find # nna or # all-grid
        const buttons = await page.$$('aside button');
        for (const btn of buttons) {
            const text = await page.evaluate(el => el.textContent, btn);
            if (text.includes('#') || text.includes('nna') || text.includes('all-grid')) {
                console.log('Clicking channel button:', text.trim());
                await btn.click();
                await new Promise(r => setTimeout(r, 1500));
                break;
            }
        }
        await page.screenshot({ path: 'C:/Users/DELL/.gemini/antigravity-ide/brain/0faf4638-1358-4965-a11e-91ae516bd355/scratch/current_team_chat.png' });
        console.log('Screenshot 3 captured: current_team_chat.png');
    }

    await browser.close();
})();
