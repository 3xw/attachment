/* EVENT HUB
*******************************/
window.aEventHub = new Vue();

/* DIRECTIVES
*******************************/
Vue.directive('sortable', function (el, binding) {
  options = binding.value || {}
  window.Sortable.create(el, options);
});

/* FILTERS
*******************************/
Vue.filter('bytesToMegaBytes', function (input) {
  return input / 1024 / 1024;
});

Vue.filter('decimal', function (input, places) {
  if (isNaN(input)) return input;
  var factor = "1" + Array(+(places > 0 && places + 1)).join("0");
  return Math.round(input * factor) / factor;
});

Vue.filter('truncate', function(value, length) {
  if(value.length < length) {
    return value;
  }

  length = length - 3;

  return value.substring(0, length) + '...';
});

Vue.filter('icon', function (input) {
  switch(true){
    case input == 'image/jpeg':
    case input == 'image/png':
    case input == 'image/gif':
    return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
    break;
    case input.lastIndexOf('embed') != -1:
    return '<i class="fa fa-file-code-o" aria-hidden="true"></i>';
    break;
    case input == 'application/pdf':
    return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
    break;
    case input == 'video/mp4':
    case input == 'video/ogg':
    return '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
    break;
    case input == 'audio/mp3':
    case input == 'audio/ogg':
    return '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
    break;
    case input == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
    return '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
    break;
    case input == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    return '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
    break;
    case input == 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
    return '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
    break;
    default:
    return '<i class="fa fa-file-o" aria-hidden="true"></i>';
  }
});

Vue.filter('isNiceImage', function (file) {
  switch(file.type+'/'+file.subtype){
    case 'image/jpeg':
    case 'image/png':
    case 'image/gif':
    return true;
    default:
    return false;
  }
});

Vue.filter('isEmbed', function (file) {
  switch(file.type){
    case 'embed':
    return true;
    break;
    default:
    return false;
  }
});

Vue.filter('isNotEmbed', function (file) {
  switch(file.type){
    case 'embed':
    return false;
    break;
    default:
    return true;
  }
});

Vue.filter('isThumbable', function (file) {
  switch(file.type+'/'+file.subtype){
    case 'image/jpeg':
    case 'image/png':
    case 'image/gif':
    return true;
    break;
    case 'embed/youtube':
    case 'embed/vimeo':
    return (file.path)? true : false;
    break;
    default:
    return false;
  }
});

Vue.filter('isNotThumbable', function (file) {
  switch(file.type+'/'+file.subtype){
    case 'image/jpeg':
    case 'image/png':
    case 'image/gif':
    return false;
    break;
    case 'embed/youtube':
    case 'embed/vimeo':
    return (file.path)? false : true;
    break;
    default:
    return true;
  }
});
