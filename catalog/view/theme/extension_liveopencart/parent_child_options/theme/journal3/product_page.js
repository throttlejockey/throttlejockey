
function replaceOriginalPcopMethods(pcop_front) {
	pcop_front.replaceOriginalMethod('getCodePcopInputNotRequired', function(){
		let html = '';
		html+= '<div class="button-group-page" style="display:none!important;">';
		html+= pcop_front.original_methods.getCodePcopInputNotRequired();
		html+= '</div>';
		return html;
	});
}