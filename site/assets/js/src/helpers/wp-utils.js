
function selectUser(name,id){
    jQuery("#jform_created_by_id").val(id);
    jQuery("#jform_created_by").val(name);
    jQuery.jbdModal.close();	
}