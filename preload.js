const { contextBridge } = require('electron');

contextBridge.exposeInMainWorld('noop', {});
