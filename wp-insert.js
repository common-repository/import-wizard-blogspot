function BLIMWI_start(){
 
  postCount = 0;
  loadScript();

}
var startIndex = 1;
var maxResults = 500;
var totalposts;
var postCount = 0;
var both = false;
var type;

function loadScript() {
  document.querySelector('.BLIMWI-progress-bar').style.display='block';

  document.querySelector('.BLIMWI-progress').style.width='1%';
  var script = document.createElement('script');
var inputUrl = document.querySelector('input#BLIMWI-blog-url').value;
type = document.querySelector('select#import-type').value;
if(type == 'all'){
  if ( both == true){
    type = 'pages';
    both = false
  }else{
    type = 'posts';
    both = true;
  }
  
}

if (!inputUrl){
  document.querySelector('.BLIMWI-progress-text').innerText ='Please enter correct URL';
}
    var urlObj = new URL(inputUrl);
    
  var blogurl = urlObj.origin;
  
  script.type = 'text/javascript';
  script.src = blogurl+ "/feeds/"+type+"/default?alt=json-in-script&callback=get"+type+"&start-index=" + startIndex + "&max-results=" + maxResults;
 document.head.appendChild(script);
 document.querySelector('#import-button').classList.add('importing');
}

function getpages(data) {
  console.log(data);
   for (var i = 0; i < data.feed.entry.length; i++) {
     var linkObj = data.feed.entry[i].link.find(function(link) {
       return link.rel === 'alternate';
     });
     
     if (linkObj) {
       var bloggerurl = linkObj.href;
     } else {
       console.log('Alternate link not found');
     }   
   var bloggerurl = data.feed.entry[i].link[2].href;
     var postTitle = data.feed.entry[i].title.$t;
 
      var defaultImg = "/wp-content/plugins/import-wizard-blogspot/dummy.jpg";  // Replace with your default image URL
 
     var postImage = data.feed.entry[i].media$thumbnail ? data.feed.entry[i].media$thumbnail.url : defaultImg;
     var postUrl = getBeforeHtml(bloggerurl);;
     console.log(postUrl)
     var postTitle = data.feed.entry[i].title.$t;
     var postContent = data.feed.entry[i].content.$t;
     var postDate = data.feed.entry[i].updated.$t;
     

    createPOST({
       title: postTitle,
       content: postContent,
       url: postUrl,
       image: postImage,
      
       date: postDate
     }, 'page'); 
   }
 //  console.log(data.feed.entry.length)
 totalposts = data.feed.entry.length;
 document.querySelector('.BLIMWI-progress-text').innerText = 'Importing '+data.feed.entry.length+' Pages';
   // If less than maxResults posts were returned, we've gotten all posts.
   // Otherwise, increment startIndex by maxResults and load the next page.
   if (data.feed.entry.length < maxResults) {
     console.log('Fetched all posts!');
   } else {
     startIndex += maxResults;
     loadScript();
   }
 }

function getposts(data) {
 
  for (var i = 0; i < data.feed.entry.length; i++) {
    var linkObj = data.feed.entry[i].link.find(function(link) {
      return link.rel === 'alternate';
    });
    
    if (linkObj) {
      var bloggerurl = linkObj.href;
    } else {
      console.log('Alternate link not found');
    }
   
    var postTitle = data.feed.entry[i].title.$t;
        
        var defaultImg = "/wp-content/plugins/import-wizard-blogspot/dummy.jpg";  // Replace with your default image URL
        var categories = [];
if (data.feed.entry[i].category) {
  data.feed.entry[i].category.forEach(function(e){
    categories.push(e.term);
  });
}

        
        
    var postImage = data.feed.entry[i].media$thumbnail ? data.feed.entry[i].media$thumbnail.url : defaultImg;
    var postUrl = getBeforeHtml(bloggerurl);;
    var postTitle = data.feed.entry[i].title.$t;
    var postContent = data.feed.entry[i].content.$t;
    var postCategories = categories;
    var postDate = data.feed.entry[i].updated.$t;
    
   
    // Extract the necessary data from the entry...
    // This will depend on the structure of your Blogger JSON data.

    // Create a new WordPress post for each Blogger post.
   createPOST({
      title: postTitle,
      content: postContent,
      url: postUrl,
      image: postImage,
      categories: postCategories,
      date: postDate
    }, 'post'); 
  }
//  console.log(data.feed.entry.length)
totalposts = data.feed.entry.length;
document.querySelector('.BLIMWI-progress-text').innerText = 'Importing '+data.feed.entry.length+' Posts';
  // If less than maxResults posts were returned, we've gotten all posts.
  // Otherwise, increment startIndex by maxResults and load the next page.
  if (data.feed.entry.length < maxResults) {
    console.log('Fetched all posts!');
  } else {
    startIndex += maxResults;
    loadScript();
  }
}




function createPOST(e, post) {
  var url = e.url;
  var ourPostData = {
    "title" : e.title,
    "content" : e.content,
    "date": e.date,
    "modified": e.date,
    "slug": url,
    "status": "publish"
  }

console.log(e.categories);
  createCategory(e.categories)
    .then((ids) => {
      if(ids != null){
        ourPostData['categories'] = ids;
      }
      uploadImageFromUrl(e.image, function(imageUrl) {
        if (imageUrl != null) {
          ourPostData["featured_media"] = imageUrl; // Set the featured_media field to the uploaded image URL
        }
      
        // Create the post
        var createPost = new XMLHttpRequest();
        if (post == 'post'){
          createPost.open("POST", additionalData.siteURL + "/wp-json/wp/v2/posts");
        }else{
          createPost.open("POST", additionalData.siteURL + "/wp-json/wp/v2/pages");
        }
        createPost.setRequestHeader('X-WP-Nonce', additionalData.nonce);
        createPost.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
       // console.log(ourPostData);
        createPost.send(JSON.stringify(ourPostData));
        createPost.onreadystatechange = function() {
            if(createPost.readyState == 4) {
                if( createPost.status == 201 ) {
                  postCount++;
                  data = JSON.parse(createPost.response);
                  document.querySelector('.BLIMWI-progress').style.width=(postCount / totalposts) * 100+'%';
                  document.querySelector('.BLIMWI-progress-text').innerText ='Imported '+postCount+'/'+totalposts+' Posts';
                  
                  document.querySelector('.BLIMWI-progress-log').style.display='flex';
                  var link = document.createElement('span');
                  link.innerHTML='<b>Imported</b>:'+ data.title.raw;
                  document.querySelector('.BLIMWI-progress-log').appendChild(link);
                  if (postCount == totalposts){
                    document.querySelector('#import-button').classList.remove('importing');
                  document.querySelector('button#step2-button').style.display='inline';
                  
                  document.querySelector('.BLIMWI-progress-text').innerText ='Successfully Imported All ' + type;
                  if(both == true){
                    BLIMWI_start();
                  }
                  }
                } else {
                  document.querySelector('.BLIMWI-progress-text').innerText ='Sorry! An error has occurred. Please try again.';
                  console.log('Error: ' + createPost.status + ' ' + createPost.statusText);
                  console.log(createPost.responseText);
                }
            }
        }
      });
    })
    .catch((e) => {
      
      console.log('Error occurred'+e);
    });
}






function createCategory(label) {
  if(!label || label.length === 0){
    return Promise.resolve(null);
  } else {
    let promises = [];
    label.forEach(names => {
      let promise = new Promise((resolve, reject) => {
        // Check if the category exists
        var getcat = new XMLHttpRequest();
        getcat.open("GET", additionalData.siteURL + "/wp-json/wp/v2/categories?search=" + names);
        getcat.setRequestHeader('X-WP-Nonce', additionalData.nonce);
        getcat.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        getcat.send();
        getcat.onreadystatechange = function() {
          if(getcat.readyState == 4) {
            if( getcat.status == 200 ) {
              var data = JSON.parse(getcat.response);
              if (data.length > 0) {
                // Category already exists, resolve with its ID
                resolve(data[0].id);
              } else {
                // Category does not exist, create it
                var ourCatData = {
                  "name" : names
                }
                var createcat = new XMLHttpRequest();
                createcat.open("POST", additionalData.siteURL + "/wp-json/wp/v2/categories");
                createcat.setRequestHeader('X-WP-Nonce', additionalData.nonce);
                createcat.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                createcat.send(JSON.stringify(ourCatData));
                createcat.onreadystatechange = function() {
                  if(createcat.readyState == 4) {
                    if( createcat.status == 201 ) {
                      var data = JSON.parse(createcat.response);
                      // Category created, resolve with its ID
                      resolve(data.id);
                    } else {
                      reject();
                    }
                  }
                }
              }
            } else {
              reject();
            }
          }
        }
      });
      promises.push(promise);
    });
    return Promise.all(promises);
  }
}



function uploadImageFromUrl(imageUrl,callback) {
  // Get the filename from the imageUrl
  var filename = imageUrl.split('/').pop();

  // First, check if the filename already exists in the post meta data
  var checkUrl = additionalData.siteURL + '/wp-json/wp/v2/media?meta_key=image_filename&meta_value=' + encodeURIComponent(filename);
  var xhrCheck = new XMLHttpRequest();
  xhrCheck.open('GET', checkUrl, true);
  xhrCheck.setRequestHeader('X-WP-Nonce', additionalData.nonce);
  xhrCheck.onreadystatechange = function() {
    if (xhrCheck.readyState === 4) {
      if (xhrCheck.status === 200) {
        var response = JSON.parse(xhrCheck.responseText);
        if (response.length > 0) {
          // The image already exists, so use its ID
          callback(response[0].id);
        } else {
          // The image does not exist, so upload it
          var xhr = new XMLHttpRequest();
          xhr.open('GET', imageUrl, true);
          xhr.responseType = 'blob';
          xhr.onload = function() {
            if (this.status === 200) {
              var formData = new FormData();
              formData.append('file', this.response, filename);
              var uploadUrl = additionalData.siteURL + '/wp-json/wp/v2/media';
              var xhr2 = new XMLHttpRequest();
              xhr2.open('POST', uploadUrl, true);
              xhr2.setRequestHeader('X-WP-Nonce', additionalData.nonce);
              xhr2.onreadystatechange = function() {
                if (xhr2.readyState === 4) {
                  if (xhr2.status === 201) {
                    var response = JSON.parse(xhr2.responseText);
                    callback(response.id);
                  } else {
                    callback(null);
                  }
                }
              };
              xhr2.send(formData);
            }
          };
          xhr.send();
        }
      } else {
        console.log('Error: ' + xhrCheck.status + ' ' + xhrCheck.statusText);
        console.log(xhrCheck.responseText);
        callback(null);
      }
    }
  };
  xhrCheck.send();
}

function getBeforeHtml(url) {
  // remove the protocol and domain name from the url
  var path = url.replace(/^https?:\/\/[^\/]+/, "");
  // split the path by the slash character
  var segments = path.split("/");
  // get the last segment of the path
  var last = segments[segments.length - 1];
  // split the last segment by the dot character
  var parts = last.split(".");
  // get the first part of the last segment
  var beforeHtml = parts[0];
  // return the result
  return beforeHtml;
}
