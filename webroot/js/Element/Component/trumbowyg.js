Vue.component('attachment-trumbowyg',{
  template: '#attachment-trumbowyg',
  props: {
    settings: Object,
    aid:String,
    content: {type: String, default: 'coucou'},
  },
  data:function(){
    return {
      file: {},
      $trumbowyg: null,
      options: null,
      startRange: null,
      endRange: null,
      types: null,
      tags: null
    };
  },
  created: function(){
    if(window.aEventHub[this.aid] == undefined){
      window.aEventHub[this.aid] = new Vue();
    }

    window.aEventHub[this.aid].$on('browse-closed', this.openOptions);
    window.aEventHub[this.aid].$on('add', this.addBrowse);
    window.aEventHub[this.aid].$on('upload-closed', this.openOptions);
    window.aEventHub[this.aid].$on('options-success', this.optionSuccess);
  },
  mounted: function(){
    if(this.settings.trumbowyg.langs && this.settings.trumbowyg.langs[this.settings.trumbowyg.lang]){
      $.trumbowyg.langs[this.settings.trumbowyg.lang] = this.settings.trumbowyg.langs[this.settings.trumbowyg.lang];
    }
    this.$trumbowyg = $('.'+this.settings.uuid);
    this.$trumbowyg
    .trumbowyg(this.settings.trumbowyg)
    .on('attachment-upload',this.upload)
    .on('attachment-browse',this.browse);
  },
  methods: {
    setup: function(trumbowyg){
      this.trumbowyg = trumbowyg;
      this.settings.attachments = [];
      this.file = null;

      this.startRange = this.getCaretCharacterOffsetWithin(this.$trumbowyg.parent().find('.trumbowyg-editor')[0]) || 0;
      this.endRange = this.startRange;
    },
    upload: function(evt, trumbowyg){
      this.setup(trumbowyg);
      window.aEventHub[this.aid].$emit('show-upload');
    },
    browse: function(evt, trumbowyg){
      this.setup(trumbowyg);
      window.aEventHub[this.aid].$emit('show-browse');
    },
    openOptions: function(){
      this.file = this.settings.attachments.shift();
      if(this.file){
        window.aEventHub[this.aid].$emit('show-options');
      }
    },
    optionSuccess: function(args){
      this.options = args[0];
      this.$trumbowyg.trumbowyg("openModalInsert", {
        title: "Confirmez l'ajout du média?",
        callback: this.createHtmlElement
      })
    },
    getImagePath: function(options){
      var path = this.settings.url+'thumbnails/'+this.file.profile+'/';
      path += (options.width)? 'w'+options.width: 'w1200';
      path += (options.crop)? 'c'+options.cropWidth+'-'+options.cropHeight: '';
      path += '/'+this.file.path;
      return path;
    },
    getCaretCharacterOffsetWithin: function(element) {
      var caretOffset = 0;
      var doc = element.ownerDocument || element.document;
      var win = doc.defaultView || doc.parentWindow;
      var sel;
      if (typeof win.getSelection != "undefined") {
        sel = win.getSelection();
        if (sel.rangeCount > 0) {
          var range = win.getSelection().getRangeAt(0);
          var preCaretRange = range.cloneRange();
          preCaretRange.selectNodeContents(element);
          preCaretRange.setEnd(range.endContainer, range.endOffset);
          caretOffset = preCaretRange.toString().length;
        }
      } else if ( (sel = doc.selection) && sel.type != "Control") {
        var textRange = sel.createRange();
        var preCaretTextRange = doc.body.createTextRange();
        preCaretTextRange.moveToElementText(element);
        preCaretTextRange.setEndPoint("EndToEnd", textRange);
        caretOffset = preCaretTextRange.text.length;
      }
      return caretOffset;
    },
    getTextNodesIn: function(node) {
      var textNodes = [];
      if (node.nodeType == 3) {
        textNodes.push(node);
      } else {
        var children = node.childNodes;
        for (var i = 0, len = children.length; i < len; ++i) {
          textNodes.push.apply(textNodes, this.getTextNodesIn(children[i]));
        }
      }
      return textNodes;
    },
    setSelectionRange: function(el, start, end) {
      if (document.createRange && window.getSelection)
      {
        var range = document.createRange();
        range.selectNodeContents(el);
        var textNodes = this.getTextNodesIn(el);
        var foundStart = false;
        var charCount = 0, endCharCount;

        for (var i = 0, textNode; textNode = textNodes[i++]; ) {
          endCharCount = charCount + textNode.length;
          if (!foundStart && start >= charCount && (start < endCharCount || (start == endCharCount && i <= textNodes.length))) {
            range.setStart(textNode, start - charCount);
            foundStart = true;
          }
          if (foundStart && end <= endCharCount) {
            range.setEnd(textNode, end - charCount);
            break;
          }
          charCount = endCharCount;
        }

        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
      } else if (document.selection && document.body.createTextRange)
      {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(true);
        textRange.moveEnd("character", end);
        textRange.moveStart("character", start);
        textRange.select();
      }
    },
    createHtmlElement: function(values){
      var options = this.options;
      var html = '<img';
      var classes = 'img-responsive img-fluid ';
      classes += (options.classes)? options.classes+' ': '';
      classes += (options.align)? options.align+' ': '';
      html += ' class=\'' + classes + '\'';
      if(options.alt){
        html += ' alt=\'' + options.alt.replace(/['"]+/g, '') + '\'';
      }
      html += ' src=\'' + this.getImagePath(options) + '\'';
      html += ' />';

      var node = $(html)[0];

      var htmlElem = this.$trumbowyg.parent().find('.trumbowyg-editor')[0];
      this.setSelectionRange(htmlElem, this.startRange, this.endRange);
      var range, sel;
      sel = window.getSelection();
      if (sel.rangeCount && sel.getRangeAt) {
        range = sel.getRangeAt(0);
      }
      if (range) {
        sel.removeAllRanges();
        sel.addRange(range);
      }
      this.$trumbowyg.trumbowyg('saveRange');
      range = this.$trumbowyg.trumbowyg('getRange');
      range.deleteContents();
      range.insertNode(node);
      return true;
    }
  }
});
