/*
    Created by Sean Hume - s320298
*/
// set variables.
var CACHE_TITLE = 'SprinklerSites';
var CACHE_VERSION = 'v0.0.30';
var CACHE_NAME = CACHE_TITLE + '-' + CACHE_VERSION;
var urlsToCache = [

  '/css/style.css',
  '/control',
  '/scripts/control.js',
  '/scripts/jquery.js',
  '/errordocs/err001',
  '/errordocs/err002',
  '/errordocs/err003',
  '/img/logo1.png',
  '/scripts/jquery.imagemapster.min.js',
  '/scripts/dataManager.js',
  '/img/House%20Plan.png'
];

var POST_db;
var STATUS_db;
var post_data;


self.addEventListener('install', function(event) {
  // Perform install steps
  // skipps the waiting of it's predecessor.
  self.skipWaiting();
  event.waitUntil(
    // opens cache and loads all URLs into it.
      caches.open(CACHE_NAME)
        .then(function(cache) {
          return cache.addAll(urlsToCache);
        })
  );
  console.log('[SW] Installed Service Worker.');
});

self.addEventListener('activate', (event) => {
  // Cleans up old caches, identified by lesser version numbers.
  // Claims all the open clients to this serviceWorker.
  self.clients.claim();
  event.waitUntil(
    caches.keys()
    .then((keyList) => {
      return Promise.all(keyList.map((cacheName) => {
        if (cacheName !== CACHE_NAME && cacheName.indexOf(CACHE_TITLE) === 0) {
          return caches.delete(cacheName);
        }
      }));
    })
    );
    // Open dataBases.
    openRequestDatabase();
    openStatusDatabase();
    // Get status data from server.
    updateStatusData();
    
    console.log('[SW] Activated Service Worker.')
});

self.addEventListener('fetch', function(event) {
  // Listens to all fetch requests, responding with cached files is available, and gets live versions if not.
  // If the request is a GET, it's most likely for a content.
  if (event.request.clone().method === 'GET') {
    event.respondWith(
      // Try to get request from cache.
      caches.match(event.request.clone())
        .then(function(resp) {
          if (resp) {
            // Cache hit - return response
            // console.log(`[SW] Using Cached page for: ${event.request.url}`);
            return resp;
          } else {
            // No item in the cache matches the request, getting it from the web.
            // console.log(`[SW] Page not found in cache, searching web for: ${event.request.clone().url}`);
            return fetch(event.request.clone());
          }
        })
    );
    
  } else if (event.request.clone().method === 'POST') {
    // If request is a POST, then most likely a ajax request.
    if (RegExp("^(.*\/|\/?)(comm\/getStatus)(\.php)?\/?$").test(event.request.url)) {
      // if the request goes to /comm/getStatus, try to get up-to-date server content first, then get from cache.
      event.respondWith(
        fetch(event.request)
          .then((response) => {
            return response;
          })
          .catch((err) => {
            // ===== Known Issue: tried to get the 'StatusIDBfuncSet.getData' to work, but the promise is bypassed and the respondWith is completed with an error before the promise returns any values.
            // I could not correctly implement this, however, the gathering and saving of the data is functional. The attempted code is commented bellow.
            console.log('[SW] Error in server request, Probably offline. Custom response containing info from the database was meant to be sent but could not due to known issue.');
            return new Response(new Blob(), {'status': 404});
            /*
            event.waitUntil(
              //Get old data from Status IDB.

              // ====ISSUE====: Returns promise, and execution goes ahead, returning invalid response before '.then' is called.
               StatusIDBFuncSet.getData(status_data.loc, StatusIDBSettings.tables[0].tableName).then((result) => {

                console.log(new Response(JSON.stringify(result), {"status" : 200, "statusText": "FROM_LOCAL", headers: {'Content-Type': 'application/json'}}));
                return new Response(JSON.stringify(result), {"status" : 200, "statusText": "FROM_LOCAL", headers: {'Content-Type': 'application/json'}});
              }).catch((result) => {
                //  Catches issue with getting the data from the database and returns error message.
                var jsonError = {
                  "connError": {
                      networkerror: true,
                      errorpage: null,
                      fromcache: true
                  }
                };
                var blob = new Blob([JSON.stringify(jsonError)], { type: "application/json"});
                console.log(`[SW] Real-time control action could not be completed by server or local DB, returning error response.`);
                return new Response(blob, {"status" : 200, "statusText": "FROM_LOCAL", headers: {"Content-Type" : "application/json"}});
              })
            )*/
          })
      );
      
    } else {
      // Connection is from/for Edit Times and is to be stored if no connection is made.
      event.respondWith(
        fetch(event.request.clone())
        .then((response) => {
          return response;
        })
        .catch((err) => {
          // SAVE REQUEST FOR FUTURE SEND. RETURN CONFERMATION.
          savePostRequests(event.request.clone().url, post_data);

          var jsonError = {
            "connError": {
              networkerror: true,
              errorpage: null,
              cached: true
            }
          };
          var blob = new Blob([JSON.stringify(jsonError)], { type: "application/json"});
          console.log(`[SW] TimeData could not be sent to server, likely due to network issues, saving and will send later.`);
          savePostRequests(event.request.url, post_data);
          return new Response(blob, {"status" : 202, "statusText": "SAVED_TO_BE_SENT", headers: {"Content-Type" : "application/json"}});
        })
        )
      }
    }
  });
  
  self.addEventListener('message', (event) => {
  if (event.data.hasOwnProperty('post_data')) {
    post_data = event.data.post_data;
  } else if (event.data.hasOwnProperty('status_data')) {
    status_data = event.data.status_data;
  } else if (event.data.hasOwnProperty('reloadStatus')) {
    updateStatusData();
  }
});


function getObjectStore(db, storeName, mode='readwrite') {
  // retrieve Object Store.
  return db.transaction(storeName, mode).objectStore(storeName);
}
// Setup STATUS IDB local database

function updateStatusData() {

  var requestURL = 'comm/getStatus';
  var method = 'POST';
  var headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  };
  var body = {
    all: true
  }

  jsonBody = JSON.stringify(body);

  fetch(requestURL, {
    headers: headers,
    method: method,
    body: jsonBody
  }).then((response) => {
    if (parseInt(response.clone().status) < 400) {
      // fetch was successful, store it in the IDB.
      response.clone().json().then((result) => {
        for(var i in result.zoneStatus){
          StatusIDBFuncSet.addData(StatusIDBSettings.tables[0].tableName, result.zoneStatus[i]);
        }
      }).catch((err) => {
        console.log(`[SW] ERROR in status json: ${err}`);
        console.log(response.clone().json());
      })
    } else {
      console.log('[SW] Server returned error for /comm/getStaus:', response.clone().status);
    }
  }).catch((err) => {
    console.log('[SW] Failed to get new status for loc: ', loc);
  })
}



/*
 The following IDB interactions are based of a stackOverflow comment by 'kyunghwanjung',
 https://stackoverflow.com/questions/31703419/how-to-import-json-file-into-indexeddb.
*/

var StatusIDBSettings = {
  name: 'status_storage',
  version: 1,
  tables: [{
    tableName: 'sprinkler_status',
    keypath: 'seq',
    autoIncrement: true,
    index: ['id', 'status', 'staTime', 'finTime', 'duration', 'title'],
    unique: [false, false, false, false, false, false]
  }]
};


function openStatusDatabase() {
  var indexedDBOpenRequest = indexedDB.open('status_storage');
  
    indexedDBOpenRequest.onerror = (error) => {
      console.log("[SW] ERROR: An error occurred and the IDB database could not be made.");
    }

    indexedDBOpenRequest.onupgradeneeded = (event) => {
      // Executes if the database needs to update.
      var db = event.target.result;

        for (var i in StatusIDBSettings.tables) {
            var OS = db.createObjectStore(StatusIDBSettings.tables[i].tableName, {
                keyPath: StatusIDBSettings.tables[i].keyPath,
                autoIncrement: StatusIDBSettings.tables[i].autoIncrement
            });
        }
    }

    indexedDBOpenRequest.onsuccess = () => {
      STATUS_db = indexedDBOpenRequest.result;
    }
}

var StatusIDBFuncSet = {
  //write
  addData: function(table, data) {
    // Opens the IDB, when successful calls the onsuccess.
    var req = indexedDB.open(StatusIDBSettings.name, StatusIDBSettings.version);

    // If opening is successful, add the data to the IDB.
    req.onsuccess = (event) => {
      // try to add it, catching a failure.
      try {
          var db = req.result;
          var transaction = db.transaction(table, "readwrite");
          var objectStore = transaction.objectStore(table);
          var objectStoreRequest = objectStore.put(data, data.id);
      } catch (e) {
          console.log("addDataFunction table or data null error");
          console.log(e);
      }
    };
    // Failure call used for debugging.
    req.onerror = (event) => {
        console.log("addData indexed DB open fail");
    };
  }
}

StatusIDBFuncSet.getData = (key, table) => {
  return new Promise ((resolve) => {
    try {
      // open the IDB.
      var req = indexedDB.open(StatusIDBSettings.name, StatusIDBSettings.version);
  
      // Only when IDB successfully opens get the data.
      req.onsuccess = (event) => {
        var db = req.result;
        var transaction = db.transaction(table, 'readonly');
        var objectStore = transaction.objectStore(table);
  

        var result = objectStore.get(key);
        result.onsuccess = (data) => {
          return resolve(data.target.result);
        }
        // return result.target.result;
      };
    } catch (e) {
      console.error(e);
    }
  });
};

// Setup REQUEST IDB local Database
function openRequestDatabase() {
  var indexedDBOpenRequest = indexedDB.open('request_storage');

  indexedDBOpenRequest.onerror = (error) => {
    console.log("[SW] ERROR: An error occurred and the IDB database could not be made.");
  }

  indexedDBOpenRequest.onupgradeneeded = () => {
    // Executes if the database needs to update.
    indexedDBOpenRequest.result.createObjectStore('post_requests', {
      autoIncrement:  true, keyPath: 'id'
    });
  }

  indexedDBOpenRequest.onsuccess = () => {
    POST_db = indexedDBOpenRequest.result;
  }
}


function savePostRequests(url, payload) {
  // get object store and save payload into it.
  var request = getObjectStore(POST_db, 'post_requests', 'readwrite').add({
    url: url,
    payload: payload,
    method: 'POST'
  });

  request.onsuccess = (event) => {
    console.log('[SW] Command Request saved to IDB');
  }

  request.onerror = (err) => {
    console.log(`[SW] ERROR: Could not save request to IDB: ${err}`);
  }
}

self.addEventListener('sync', (event) => {
  console.log("Now online!");
  if (event.tag === 'sendTimeData') {
    event.waitUntil(
      sendPostToServer()
      )
  }
});

function sendPostToServer() {
  // Open the database if the service worker has not yet activated.
  if (POST_db == undefined) {
    return;
  }

  var savedRequests = [];
  var req = getObjectStore(POST_db, 'post_requests').openCursor();

  req.onsuccess = async (event) => {
    var cursor = event.target.result;

    if (cursor) {
      // Keep moving the cursor forward to new saved requests.
      savedRequests.push(cursor.value);
      cursor.continue();
    } else {
      // At this point all post requests have been collected from IDB.
      for (let savedRequest of savedRequests) {
        // Send them to the server.
        console.log('[SW] Saved request sent to server');
        var requestURL = savedRequest.url;
        var payload = JSON.stringify(savedRequest.payload);
        var method = savedRequest.method;
        var headers = {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        };

        fetch(requestURL, {
          headers: headers,
          method: method,
          body: payload
        }).then((response) => {
          if (response.status < 400) {
            // fetch was successful, remove it from the IDB.
            getObjectStore(POST_db, 'post_requests', 'readwrite').delete(savedRequest.id);
          }
        }).catch((err) => {
          //Triggered if the network is still down & will be replayed when the network connects again.
          console.error('[SW] Failed to send saved POST: ', error);
          // throw error so background sync keeps trying.
          throw error;
        })
      }
    }
  }
}