$(function(){
	if(CMS.ordering){
		$(".sortable").sortable({
			update:updateSort
		}).disableSelection();
		
		function updateSort(){
			var list = $( ".sortable" ).sortable( "toArray" );
			$.post(CMS.ordering_path, {ids:list, ordering:ordering});
		}
	}
});