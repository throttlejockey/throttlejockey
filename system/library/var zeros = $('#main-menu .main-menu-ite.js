var zeros = $('#main-menu .menu-item .count-zero').parent('a');
// var parent_elem = $('#main-menu > ul.j-menu');
// var testEl = $("#main-menu menu-item-c389");

// console.log(testEl);
// var len = testEl.parents('#main-menu > ul.j-menu');
// console.log('len', len);
 zeros.each( function(el){
    // console.log($(this));
    var parentEls = $( this ).parentsUntil('#main-menu.main-menu > ul.j-menu:first-child');
    var parentElsNfo = $( this ).parentsUntil('#main-menu > ul.j-menu:first-child')    
        .map(function() {
            return this.tagName;
        })
        .get()
        .join( ", " );

        console.log(parentEls);
        console.log("p: ", parentElsNfo); // $( "b" ).append( "<strong>" + parentEls + "</strong>" );
        console.log("pl: ", parentEls.length);
        
        if( parentEls.length < 4 ) {
            console.log('First Level of Menu');   
            parentEls.css('background', 'red');         
            console.log(parentEls);
            console.log("p: ", parentElsNfo); // $( "b" ).append( "<strong>" + parentEls + "</strong>" );
            console.log("pl: ", parentEls.length);
        } else if(parentEls.length >= 4){

            // var child_zeros = $(this).find('.count-zero'.length);
            // var num_subs = $(this).find('li').length;
            
            // console.log('child_zeros: ', child_zeros);
            // console.log('num_subs: ', num_subs);

            // if( child_zeros === num_subs ) {
            //     $(this).css('background', 'red');
            // }

            // console.log(' ');
            // console.log("I'm in it deep now!");
            // console.log(parentEls);
            // // console.log("p: ", parentElsNfo); // $( "b" ).append( "<strong>" + parentEls + "</strong>" );
            // // console.log("pl: ", parentEls.length);  
            // // console.log(' ');

            // // console.log('childzerosLen ', childzerosLen);  
            // console.log('child_zeros ', child_zeros);   

            // console.log(' ');
            // console.log(' ');

            // $(this).css('display','none');
            
        }
    });

 