function deleteDatatableSearch(){
    const datatableSearch = $(".dataTables_processing");
    if(datatableSearch){
        datatableSearch.remove();
    }
 }
 
deleteDatatableSearch();
 
$(document).ajaxStart(function(e) {
    deleteDatatableSearch();
    spinnerOverride();
    showAndHideModal();
});
 
  
$(document).ajaxStop(function(e) {
    deleteDatatableSearch();
    spinnerOverride();
});

$(document).ready(function() {
    deleteDatatableSearch();
})

function spinnerOverride(){
    
    const sorting = $('.sorting');
    const search = $('.dataTables_filter input[type="search"]');
    
    sorting.on('click',function(){
        showAndHideModal();
    })

    search.on('change',function(){
        showAndHideModal();
    })
    
}



;