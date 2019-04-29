function showPopup()
{
    jQuery("#popup").fadeToggle();
    jQuery("#popupbackground").fadeToggle();
}

function smsCounter()
{
    text = jQuery("#smsText").val();
    text = getTemplateValues(text);
    jQuery("#smsPreview").text(text);
    
    var smsCount = 0;
    var smsLen = jQuery("#smsPreview").val().length;
    var customers = "";
    var customersCount = parseInt(jQuery.trim(jQuery("#snippet-countup").text()));

    if(jQuery("#unicode").is(':checked'))
    {
        smsCount = Math.floor(smsLen / 70 + (smsLen % 70 > 0));
    }
    else 
    {
        smsCount = Math.floor(smsLen / 160 + (smsLen % 160 > 0));
    }

    if(jQuery.isNumeric(customersCount))
    {
        customers = ", " + v_smsmarketing_jstotalcustomers + " " + customersCount + ", " + v_smsmarketing_jstotalsms + (customersCount * smsCount);
    }   
    
    jQuery("#labelSMS").text("("+v_smsmarketing_jschars + jQuery("#smsPreview").val().length + ", " + v_smsanswers_sms + ": " + smsCount + customers+")");
}

function addToTextBox(text)
{
    jQuery("#smsText").atCaret('insert',text);
    smsCounter();
}

function addRecipient(number)
{
    jQuery("#recipients").val(jQuery("#recipients").val()+number+", ");
    jQuery('span[data="'+number+'"]').css("text-decoration", "line-through");
}

function confirmSend(send, sms)
{
    if(confirmLink(this, send+' ', ' '+sms+'?'))
    {
        jQuery("#ajaxLoader_submitSend").removeClass("invisibility");
        return true;   
    }
    return false;
}

function toggleCouponVariables()
{
    if(jQuery('#couponSelect').val() == -1)
    {
        jQuery('#couponVariables').hide();
    }
    else 
    {
        jQuery('#couponVariables').removeClass('hidden');
        jQuery('#couponVariables').show();
    }
}

function textSender()
{
    if(jQuery("#selectTextSender").val() != -1)
    {
        jQuery("#newTextSender").hide();
    }
    else
    {
        jQuery("#newTextSender").show();
    }
}

function ownSender()
{
    if(jQuery("#selectOwnSender").val() != -1)
    {
        jQuery("#newOwnSender").hide();
    }
    else
    {
        jQuery("#newOwnSender").show();
    }
}

function confirmTest(event, message)
{
    var answer = confirm(message)
    if (!answer){
        event.preventDefault();
    }
    
    return false;  
} 

function confirmLink(theLink, test5,test6)
{
    if(this.txt1>0 && this.totalsms>0)
    {
        var is_confirmed = confirm(test5+this.totalsms+test6);
        if (is_confirmed) {
            theLink.href += '&is_js_confirmed=1';
        }
        return is_confirmed;
    }
} 

String.prototype.replaceAll = function (find, replace) {
    var str = this;
    return str.replace(new RegExp(find, 'g'), replace);
};

function toggleInfo(id)
{
    if(jQuery('#toggleText'+id).css('display') == 'none')
    {
        jQuery('#toggleButton'+id).attr('src', minus);
        jQuery('#toggleText'+id).show();
        jQuery('#toggleText'+id).removeClass("hidden");
    } else {
        jQuery('#toggleButton'+id).attr('src', plus);
        jQuery('#toggleText'+id).hide();
    }
}

function setNotice(notice, element)
{
    jQuery('#textArea'+element).atCaret('insert',notice);
    countHook(element);
}

function countHook(element) 
{
    text1 = jQuery('#textArea'+element).val();
    text1 = getTemplateValues(text1);
    jQuery('#textfake'+element).text(text1);

    len = text1.length;
    var total = 0;
    
    if(unicode)
    {
        total = Math.floor(len / 70 + (len % 70 > 0));
    }
    else 
    {
        total = Math.floor(len / 160 + (len % 160 > 0));
    }

    jQuery('#label'+element).text(v_adminsms_preview+' ('+chartext+': '+len+', '+v_adminsms_jstotalsms+' '+total+')');
}

function popup_title(obj)
{
    var $this = jQuery(obj);
    var title = $this.attr('title');
    jQuery(".popup-title").fadeOut(500, function() {
        jQuery(this).remove();
    });
    var position = $this.position();
    var popup = jQuery("<p class='popup-title' style='display: none;'>" + title + "</p>");
    jQuery("body").append(popup);
    popup.css("position", "absolute")
        .css("top", (position.top-20)+"px")
        .css("left", (position.left-20)+"px")
        .css("max-width", "400px")
        .css("background","black")
        .css("color", "white")
        .css("text-align", "left")
        .css("padding", "5px")
        .css("cursor", "pointer")
        .fadeIn(500);
    popup.click(function() {
        $this.attr("title", jQuery(this).text());
        jQuery(popup).fadeOut(500, function() { jQuery(this).remove() });
    });
}

function vat(){
    alert(document.forms['editAccount']['country0'].value);
}

function isVAT(DIC2, country02)
{
    jQuery(".loader").removeClass("hidden");
    if(!DIC2)
    {
        jQuery(".vat").css("background-color","#fff");
        jQuery(".vat").css("border","initial solid 1px");
        jQuery(".vatErr").addClass("hidden");
        jQuery(".loader").addClass("hidden");
        return;
    }

    
    if(country02 == "Czech Republic" || country02=="Slovakia" || country02=="Slovenia"  || country02=="Bulgaria"  || country02=="Romania"    || country02=="Spain" || country02=="Sweden" || country02=="United Kingdom"   || country02=="Austria" || country02=="Belgium"  || country02=="Cyprus"  || country02=="Denmark"  || country02=="France" || country02=="Germany"   || country02=="Greece" || country02=="Hungary"  || country02=="Ireland" || country02=="Italy" || country02=="Latvia"  || country02=="Lithuania"     || country02=="Luxembourg" || country02=="Netherlands" || country02=="Poland"    || country02=="Portugal" || country02=="Estonia" || country02=="Malta"   || country02=="Finland"   || country02=="Croatia")
    {
              
        var pref_country;
        var vysl;
        var wait = true;
        
        switch(country02)
        {
            case "Czech Republic": pref_country = "CZ"; break;
            case "Slovakia": pref_country = "SK"; break;
            case "Slovenia": pref_country = "SI"; break;  
            case "Bulgaria": pref_country = "BG"; break; 
            case "Romania": pref_country = "RO"; break;     
            case "Spain": pref_country = "ES"; break;
            case "Sweden": pref_country = "SE"; break;
            case "United Kingdom": pref_country = "GB"; break; 
            case "Austria": pref_country = "AT"; break; 
            case "Belgium": pref_country = "BE"; break; 
            case "Cyprus": pref_country = "CY"; break; 
            case "Denmark": pref_country = "DK"; break;      
            case "France": pref_country = "FR"; break;     
            case "Germany": pref_country = "DE"; break; 
            case "Greece": pref_country = "EL"; break; 
            case "Hungary": pref_country = "HU"; break; 
            case "Ireland": pref_country = "IE"; break;     
            case "Italy": pref_country = "IT"; break;
            case "Latvia": pref_country = "LV"; break;
            case "Lithuania": pref_country = "LT"; break;
            case "Luxembourg": pref_country = "LU"; break;
            case "Netherlands": pref_country = "NL"; break;  
            case "Poland": pref_country = "PL"; break; 
            case "Portugal": pref_country = "PT"; break; 
            case "Estonia": pref_country = "EE"; break; 
            case "Malta": pref_country = "MT"; break; 
            case "Finland": pref_country = "FI"; break; 
            case "Croatia": pref_country = "HR"; break;         
        }

        DIC2 = DIC2.replace(" ", "");
        DIC2 = DIC2.replace(".", "");
        DIC2 = DIC2.replace("-", "");
       
        prefix = DIC2.substr(0,2);
        prefix = prefix.toLowerCase();
        country = pref_country.toLowerCase();

        
        if(prefix == country){
            DIC2 = DIC2.substr(2);
        }
 
        jQuery.getJSON('http://isvat.appspot.com/'+pref_country+'/'+DIC2+'/?callback=?', function(data){
                    
            if(data){
                jQuery(".vat").css("background-color","#f1fbe5");
                jQuery(".vat").css("border","#8cce3b 1px solid");
                jQuery(".vatErr").addClass("hidden");
            } else {
                jQuery(".vat").css("background-color","#fef1ec");
                jQuery(".vat").css("border","#cd0a0a 1px solid");
                jQuery(".vatErr").removeClass("hidden");
            }
            jQuery(".loader").addClass("hidden");
        });

                               
    } else {
        jQuery(".vat").css("background-color","#fff");
        jQuery(".vat").css("border","#aaa solid 1px");
        jQuery(".vatErr").addClass("hidden");
        jQuery(".loader").addClass("hidden");
        return;
    }
}

var pismeno = "";

function setselected(number)
{
    jQuery("#recipients").val(jQuery("#recipients").val() + number + ",");
}

function confirmLink(theLink, test5,test6)
{
    var is_confirmed = confirm(test5+test6);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }
    return is_confirmed;
} 