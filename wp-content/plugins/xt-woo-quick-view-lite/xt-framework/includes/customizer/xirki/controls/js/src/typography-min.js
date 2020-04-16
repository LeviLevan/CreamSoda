wp.customize.controlConstructor["xirki-typography"]=wp.customize.xirkiDynamicControl.extend({initXirkiControl:function(){"use strict";var control=this,value=control.setting._value,picker;control.renderFontSelector();control.renderBackupFontSelector();control.renderVariantSelector();if("undefined"!==typeof control.params.default["font-size"]){this.container.on("change keyup paste",".font-size input",function(){control.saveValue("font-size",jQuery(this).val())})}if("undefined"!==typeof control.params.default["line-height"]){this.container.on("change keyup paste",".line-height input",function(){control.saveValue("line-height",jQuery(this).val())})}if("undefined"!==typeof control.params.default["margin-top"]){this.container.on("change keyup paste",".margin-top input",function(){control.saveValue("margin-top",jQuery(this).val())})}if("undefined"!==typeof control.params.default["margin-bottom"]){this.container.on("change keyup paste",".margin-bottom input",function(){control.saveValue("margin-bottom",jQuery(this).val())})}if("undefined"!==typeof control.params.default["letter-spacing"]){value["letter-spacing"]=jQuery.isNumeric(value["letter-spacing"])?value["letter-spacing"]+"px":value["letter-spacing"];this.container.on("change keyup paste",".letter-spacing input",function(){value["letter-spacing"]=jQuery.isNumeric(jQuery(this).val())?jQuery(this).val()+"px":jQuery(this).val();control.saveValue("letter-spacing",value["letter-spacing"])})}if("undefined"!==typeof control.params.default["word-spacing"]){this.container.on("change keyup paste",".word-spacing input",function(){control.saveValue("word-spacing",jQuery(this).val())})}if("undefined"!==typeof control.params.default["text-align"]){this.container.on("change",".text-align input",function(){control.saveValue("text-align",jQuery(this).val())})}if("undefined"!==typeof control.params.default["text-transform"]){jQuery(control.selector+" .text-transform select").selectWoo().on("change",function(){control.saveValue("text-transform",jQuery(this).val())})}if("undefined"!==typeof control.params.default["text-decoration"]){jQuery(control.selector+" .text-decoration select").selectWoo().on("change",function(){control.saveValue("text-decoration",jQuery(this).val())})}if("undefined"!==typeof control.params.default.color){picker=this.container.find(".xirki-color-control");picker.wpColorPicker({change:function(){setTimeout(function(){control.saveValue("color",picker.val())},100)},clear:function(event){setTimeout(function(){control.saveValue("color","")},100)}})}},renderFontSelector:function(){var control=this,selector=control.selector+" .font-family select",data=[],standardFonts=[],googleFonts=[],value=control.setting._value,fonts=control.getFonts(),fontSelect,controlFontFamilies;if(!_.isUndefined(fonts.standard)){_.each(fonts.standard,function(font){standardFonts.push({id:font.family.replace(/&quot;/g,"&#39"),text:font.label})})}if(!_.isUndefined(fonts.google)){_.each(fonts.google,function(font){googleFonts.push({id:font.family,text:font.family})})}controlFontFamilies={};if(!_.isUndefined(control.params)&&!_.isUndefined(control.params.choices)&&!_.isUndefined(control.params.choices.fonts)&&!_.isUndefined(control.params.choices.fonts.families)){controlFontFamilies=control.params.choices.fonts.families}data=jQuery.extend({},controlFontFamilies,{default:{text:xirkiL10n.defaultCSSValues,children:[{id:"",text:xirkiL10n.defaultBrowserFamily},{id:"initial",text:"initial"},{id:"inherit",text:"inherit"}]},standard:{text:xirkiL10n.standardFonts,children:standardFonts},google:{text:xirkiL10n.googleFonts,children:googleFonts}});if(xirkiL10n.isScriptDebug){console.info('Xirki Debug: Font families for control "'+control.id+'":');console.info(data)}data=_.values(data);fontSelect=jQuery(selector).selectWoo({data:data});if(value["font-family"]||""===value["font-family"]){value["font-family"]=xirki.util.parseHtmlEntities(value["font-family"].replace(/'/g,'"'));fontSelect.val(value["font-family"]).trigger("change")}fontSelect.on("change",function(){control.saveValue("font-family",jQuery(this).val());control.renderBackupFontSelector();control.renderVariantSelector()})},renderBackupFontSelector:function(){var control=this,selector=control.selector+" .font-backup select",standardFonts=[],value=control.setting._value,fontFamily=value["font-family"],fonts=control.getFonts(),fontSelect;if(_.isUndefined(value["font-backup"])||null===value["font-backup"]){value["font-backup"]=""}if("inherit"===fontFamily||"initial"===fontFamily||"google"!==xirki.util.webfonts.getFontType(fontFamily)){jQuery(control.selector+" .font-backup").hide();return}jQuery(control.selector+" .font-backup").show();if(!_.isUndefined(fonts.standard)){_.each(fonts.standard,function(font){standardFonts.push({id:font.family.replace(/&quot;/g,"&#39"),text:font.label})})}fontSelect=jQuery(selector).selectWoo({data:standardFonts});if("undefined"!==typeof value["font-backup"]){fontSelect.val(value["font-backup"].replace(/'/g,'"')).trigger("change")}fontSelect.on("change",function(){control.saveValue("font-backup",jQuery(this).val())})},renderVariantSelector:function(){var control=this,value=control.setting._value,fontFamily=value["font-family"],selector=control.selector+" .variant select",data=[],isValid=false,fontType=xirki.util.webfonts.getFontType(fontFamily),variants=["","regular","italic","700","700italic"],fontWeight,variantSelector,fontStyle;if("google"===fontType){variants=xirki.util.webfonts.google.getVariants(fontFamily)}if(!_.isUndefined(control.params)&&!_.isUndefined(control.params.choices)&&!_.isUndefined(control.params.choices.fonts)&&!_.isUndefined(control.params.choices.fonts.variants)){if(!_.isUndefined(control.params.choices.fonts.variants[fontFamily])){variants=control.params.choices.fonts.variants[fontFamily]}}if(xirkiL10n.isScriptDebug){console.info('Xirki Debug: Font variants for font-family "'+fontFamily+'":');console.info(variants)}if("inherit"===fontFamily||"initial"===fontFamily||""===fontFamily){value.variant="inherit";variants=[""];jQuery(control.selector+" .variant").hide()}if(1>=variants.length){jQuery(control.selector+" .variant").hide();value.variant=variants[0];control.saveValue("variant",value.variant);if(""===value.variant||!value.variant){fontWeight="";fontStyle=""}else{fontWeight=!_.isString(value.variant)?"400":value.variant.match(/\d/g);fontWeight=!_.isObject(fontWeight)?"400":fontWeight.join("");fontStyle=value.variant&&-1!==value.variant.indexOf("italic")?"italic":"normal"}control.saveValue("font-weight",fontWeight);control.saveValue("font-style",fontStyle);return}jQuery(control.selector+" .font-backup").show();jQuery(control.selector+" .variant").show();_.each(variants,function(variant){if(value.variant===variant){isValid=true}data.push({id:variant,text:variant})});if(!isValid){value.variant="regular"}if(jQuery(selector).hasClass("select2-hidden-accessible")){jQuery(selector).selectWoo("destroy");jQuery(selector).empty()}variantSelector=jQuery(selector).selectWoo({data:data});variantSelector.val(value.variant).trigger("change");variantSelector.on("change",function(){control.saveValue("variant",jQuery(this).val());if("string"!==typeof value.variant){value.variant=variants[0]}fontWeight=!_.isString(value.variant)?"400":value.variant.match(/\d/g);fontWeight=!_.isObject(fontWeight)?"400":fontWeight.join("");fontStyle=-1!==value.variant.indexOf("italic")?"italic":"normal";control.saveValue("font-weight",fontWeight);control.saveValue("font-style",fontStyle)})},getFonts:function(){var control=this,initialGoogleFonts=xirki.util.webfonts.google.getFonts(),googleFonts={},googleFontsSort="alpha",googleFontsNumber=0,standardFonts={};if(!_.isEmpty(control.params.choices.fonts.google)){if("alpha"===control.params.choices.fonts.google[0]||"popularity"===control.params.choices.fonts.google[0]||"trending"===control.params.choices.fonts.google[0]){googleFontsSort=control.params.choices.fonts.google[0];if(!isNaN(control.params.choices.fonts.google[1])){googleFontsNumber=parseInt(control.params.choices.fonts.google[1],10)}googleFonts=xirki.util.webfonts.google.getFonts(googleFontsSort,"",googleFontsNumber)}else{_.each(control.params.choices.fonts.google,function(fontName){if("undefined"!==typeof initialGoogleFonts[fontName]&&!_.isEmpty(initialGoogleFonts[fontName])){googleFonts[fontName]=initialGoogleFonts[fontName]}})}}else{googleFonts=xirki.util.webfonts.google.getFonts(googleFontsSort,"",googleFontsNumber)}if(!_.isEmpty(control.params.choices.fonts.standard)){_.each(control.params.choices.fonts.standard,function(fontName){if("undefined"!==typeof xirki.util.webfonts.standard.fonts[fontName]&&!_.isEmpty(xirki.util.webfonts.standard.fonts[fontName])){standardFonts[fontName]={};if("undefined"!==xirki.util.webfonts.standard.fonts[fontName].stack&&!_.isEmpty(xirki.util.webfonts.standard.fonts[fontName].stack)){standardFonts[fontName].family=xirki.util.webfonts.standard.fonts[fontName].stack}else{standardFonts[fontName].family=googleFonts[fontName]}if("undefined"!==xirki.util.webfonts.standard.fonts[fontName].label&&!_.isEmpty(xirki.util.webfonts.standard.fonts[fontName].label)){standardFonts[fontName].label=xirki.util.webfonts.standard.fonts[fontName].label}else if(!_.isEmpty(standardFonts[fontName])){standardFonts[fontName].label=standardFonts[fontName]}}else{standardFonts[fontName]={family:fontName,label:fontName}}})}else{_.each(xirki.util.webfonts.standard.fonts,function(font,id){standardFonts[id]={family:font.stack,label:font.label}})}return{google:googleFonts,standard:standardFonts}},saveValue:function(property,value){var control=this,input=control.container.find(".typography-hidden-value"),val=control.setting._value;val[property]=value;jQuery(input).attr("value",JSON.stringify(val)).trigger("change");control.setting.set(val)}});