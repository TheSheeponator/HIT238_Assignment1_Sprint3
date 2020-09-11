$(document).ready(function () {
  // Setup Service Worker
  if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
        // Registration was successful
        console.log('ServiceWorker registration successful with scope: ', registration.scope);
      }, function(err) {
        // registration failed :(
        console.log('ServiceWorker registration failed: ', err);
      });
    
    // Setup Background Sync after the service worker has activated itself.
    navigator.serviceWorker.ready.then(registration => registration.sync.register('sendTimeData'))
    .then(() => {
      console.log("Sync Registered.");
    }).catch(() => {
      console.log("Sync Failed.");
    });
  }  
});