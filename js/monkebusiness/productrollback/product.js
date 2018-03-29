/**
 * Created by milan on 12/13/14.
 */
var rollbackApplied = false;
var pcRollback = {};
function openRollbackPopup(url) {
    if ($('browser_window') && typeof(Windows) != 'undefined') {
        Windows.focus('browser_window');
        return;
    }

    var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
    var width = viewport.width; // Usable window width
    var height = viewport.height; // Usable window height

    width = (width * 0.9) + 'px';
    height = (height * 0.8) + 'px';

    var dialogWindow = Dialog.info(null, {
        closable:true,
        resizable:false,
        draggable:true,
        className:'magento',
        windowClassName:'popup-window',
        title:'Product Attribute Revisions',
        top:50,
        width: width,
        height:height,
        zIndex:1000,
        recenterAuto:false,
        hideEffect:Element.hide,
        showEffect:Element.show,
        id:'browser_window',
        url:url,
        onClose:function (param, el) {
            if(rollbackApplied) {
                $('loading-mask').show();
                location.reload();
            }
        }
    });
}

function closePopup() {
    Windows.close('browser_window');
}

jQuery(document).ready(function ($){
    $('input[name^="product"],select[name^="product"],textarea[name^="product"]').each(function() {
        var attrCode = $(this).attr('id');
        if(
            attrCode == 'media_gallery_content_save'
            || attrCode == 'media_gallery_content_save_image'
            || attrCode == 'url_key_create_redirect'
            || typeof attrCode == "undefined"
            || !attrCode
        ){return;}
        if(typeof pcRollback.attrChangeCount[attrCode] != 'undefined'){
            var count = pcRollback.attrChangeCount[attrCode];
        }else{
            var count = 0;
        }

        $(this).after('<span class="attr_revisions"  attrcode="' + $(this).attr('id') + '" onlick="showAttrRevisions(\'' + $(this).attr('id') + '\')">History (<span class="count">' + count + '</span>)</span>');
    });
    $('.attr_revisions').each(function(){
        $(this).webuiPopover({
            title: 'Recent changes for attribute ' + $(this).closest('tr').find('label').eq(0).text().replace('*',''),
            content:'Content',
            closeable:true,
            placement:'auto',
            width: '900px',
            height: '300px',
            animation:'pop',
            type:'iframe',
            cache: false,
            url: pcRollback.attributeHistoryUrl + 'attribute_code/' + $(this).attr('attrcode')
        })
            .on('hide.webui.popover',function(){
                if(typeof rollbackApplied != "undefined" && rollbackApplied){
                    $('#loading-mask').show();
                    location.reload();
                }
            })
            .on('hideall.webui.popover',function(){
                if(typeof rollbackApplied != "undefined" && rollbackApplied){
                    $('#loading-mask').show();
                    location.reload();
                }
            })
        ;
    })
    $('.tab-item-link').click(function(){
        $('.attr_revisions').webuiPopover('hide');
    });
})

function showHistoryIframe(em){
    jQuery(em).prev().fadeOut(function(){
        jQuery(em).height(jQuery(em).parent().parent().height()).fadeIn();
    });
}
