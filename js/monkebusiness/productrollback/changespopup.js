//Pl
function showDifference(url){
    $('changed_attributes_grid').hide();
    $('difference_view').update('<iframe style="width:100%;height:calc(100vh - 40px);margin:0px;box-sizing:border-box" frameborder="0" src="' + url +'"></iframe>').show();
}

function backToOverview(){
    $('difference_view').hide();
    $('changed_attributes_grid').show();
}

