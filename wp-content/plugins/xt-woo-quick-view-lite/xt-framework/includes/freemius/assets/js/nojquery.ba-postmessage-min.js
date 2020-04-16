function NoJQueryPostMessageMixin(postBinding,receiveBinding){var setMessageCallback,unsetMessageCallback,currentMsgCallback,intervalId,lastHash,cacheBust=1;if(window.postMessage){if(window.addEventListener){setMessageCallback=function(callback){window.addEventListener("message",callback,false)};unsetMessageCallback=function(callback){window.removeEventListener("message",callback,false)}}else{setMessageCallback=function(callback){window.attachEvent("onmessage",callback)};unsetMessageCallback=function(callback){window.detachEvent("onmessage",callback)}}this[postBinding]=function(message,targetUrl,target){if(!targetUrl){return}target.postMessage(message,targetUrl.replace(/([^:]+:\/\/[^\/]+).*/,"$1"))};this[receiveBinding]=function(callback,sourceOrigin,delay){if(currentMsgCallback){unsetMessageCallback(currentMsgCallback);currentMsgCallback=null}if(!callback){return false}currentMsgCallback=setMessageCallback(function(e){switch(Object.prototype.toString.call(sourceOrigin)){case"[object String]":if(sourceOrigin!==e.origin){return false}break;case"[object Function]":if(sourceOrigin(e.origin)){return false}break}callback(e)})}}else{this[postBinding]=function(message,targetUrl,target){if(!targetUrl){return}target.location=targetUrl.replace(/#.*$/,"")+"#"+ +new Date+cacheBust+++"&"+message};this[receiveBinding]=function(callback,sourceOrigin,delay){if(intervalId){clearInterval(intervalId);intervalId=null}if(callback){delay=typeof sourceOrigin==="number"?sourceOrigin:typeof delay==="number"?delay:100;intervalId=setInterval(function(){var hash=document.location.hash,re=/^#?\d+&/;if(hash!==lastHash&&re.test(hash)){lastHash=hash;callback({data:hash.replace(re,"")})}},delay)}}}return this}