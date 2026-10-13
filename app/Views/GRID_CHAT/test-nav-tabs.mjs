import { chromium } from 'playwright';

const outDir = 'C:\\Users\\DELL\\AppData\\Local\\Temp\\claude\\C--Program-Files-Ampps-www-app-Views\\52a4a48c-586e-4c1b-80a9-2906489761eb\\scratchpad';
const browser = await chromium.launch();
const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
const page = await context.newPage();
await page.goto('http://localhost:5173', { waitUntil: 'networkidle' });
await page.waitForTimeout(800);

const tabs = ['tab-moments', 'tab-livestreaming', 'tab-channels', 'tab-calls', 'tab-chats'];
for (const tab of tabs) {
  const btn = page.locator(`.mobile-tab-btn.${tab}`);
  if (await btn.count() === 0) {
    console.log(tab, '- button not found');
    continue;
  }
  await btn.click();
  await page.waitForTimeout(400);
  const navVisible = await page.locator('.mobile-bottom-nav').isVisible().catch(() => false);
  console.log(tab, '- nav visible after click:', navVisible);
  await page.screenshot({ path: `${outDir}\\navtab-${tab}.png` });
}

await browser.close();
