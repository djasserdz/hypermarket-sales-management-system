const { app, BrowserWindow, globalShortcut } = require('electron');
const path = require('path');

let win;

function createWindow() {
  win = new BrowserWindow({
    width: 1200,
    height: 800,
    icon: path.join(__dirname, 'build/hepermaretLogo.ico'),
    autoHideMenuBar: true,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false
    }
  });

win.loadURL('http://127.0.0.1:8000'); //hicham bdl URL hna ta3 domain
}
app.whenReady().then(() => {
  createWindow();

  // Ctrl+F to toggle fullscreen
  globalShortcut.register('CommandOrControl+F', () => {
    if (win) win.setFullScreen(!win.isFullScreen());
  });

  // Ctrl+S to reload window
  globalShortcut.register('CommandOrControl+S', () => {
    if (win) win.reload();
  });

  // Ctrl+Q to quit app
  globalShortcut.register('CommandOrControl+Q', () => {
    app.quit();
  });
});
app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) createWindow();
});

app.on('will-quit', () => {
  globalShortcut.unregisterAll();
});
