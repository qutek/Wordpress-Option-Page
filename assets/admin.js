;(function($){
    var element = {
        container : '.funkopt-container',
        searchInput : '#funkopt-search-input',
        notFound : '#funkopt-not-found',
        tabs : '.nav-tab-wrapper .nav-tab',
        row : '.settings-form-row',
        item : $(),
        itemsIndexed : []
    };
    /**
     * [FunkmoSettings description]
     * @type {Object}
     */
    var FunkmoSettings = {
        init : function () {
            // indexing
            $(element.row).each( function() {
                element.itemsIndexed.push( $( this ).text().replace( /\s{2,}/g, ' ' ).toLowerCase() );
            });

            $( document ).on( 'click', element.tabs, this.updateActiveTab );
            $( document ).on( 'keyup', element.searchInput, this.filterRow );
            $( document ).on( 'click', '.funkopt-browse', this.uploadFile );
            this.getActiveTab();
        },
        getActiveTab : function(){
            var activetab = '';
            $(element.row).addClass('is-hidden');

            if (typeof(localStorage) != 'undefined' ) {
                activetab = localStorage.getItem("activetab");
            }
            if (activetab != '' && $('#'+activetab+'-tab').length ) {
                $('#'+activetab+'-tab').addClass('nav-tab-active').removeClass('is-hidden');
                $(element.row+'.'+activetab).removeClass('is-hidden');
                $(element.container).find('.funkopt-section-title').text($('#'+activetab+'-tab').text());
                $(element.container).find('.funkopt-section-desc').text($('#'+activetab+'-tab').attr('data-desc'));

            } else {
                activetab = $(element.tabs+':first').attr('data-group');
                $('#'+activetab+'-tab').addClass('nav-tab-active').removeClass('is-hidden');
                $(element.row+'.'+activetab).removeClass('is-hidden');
                $(element.container).find('.funkopt-section-title').text($('#'+activetab+'-tab').text());
                $(element.container).find('.funkopt-section-desc').text($(element.tabs+':first').attr('data-desc'));
            }
        },
        updateActiveTab : function(evt){
            $(element.tabs).removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active').blur();
            var clicked_group = $(this).attr('data-group');
            // alert(clicked_group);
            if (typeof(localStorage) != 'undefined' ) {
                localStorage.setItem("activetab", clicked_group );
            }
            $(element.row).addClass('is-hidden');
            $(element.row+'.'+clicked_group).removeClass('is-hidden');
            $(element.container).find('.funkopt-section-title').text($(this).text());
            $(element.container).find('.funkopt-section-desc').text($(this).attr('data-desc'));
            evt.preventDefault();
        },
        filterRow : function(e) {
            if( e.keyCode == 13 ) { // enter
                $(element.searchInput).trigger( 'blur' );
                return true;
            }

            $(element.row).each( function() {
                element.item = $( this );
                element.item.html( element.item.html().replace( /<span class="highlight">([^<]+)<\/span>/gi, '$1' ) );
            });

            var searchVal = $.trim( $(element.searchInput).val() ).toLowerCase();
            if( searchVal.length ) {
                for( var i in element.itemsIndexed ) {
                    element.item = $(element.row).eq( i );
                    if( element.itemsIndexed[ i ].indexOf( searchVal ) != -1 )
                        element.item.removeClass( 'is-hidden' ).html( element.item.html().replace( new RegExp( searchVal+'(?!([^<]+)?>)', 'gi' ), '<span class="highlight">$&</span>' ) );
                    else
                        element.item.addClass( 'is-hidden' );
                }
            } else { 
                var active = $(element.tabs).filter( '.nav-tab-active' ).attr('data-group');
                $(element.row).not('.'+active).addClass( 'is-hidden' );
                
                // this.getActiveTab();
            }

            $(element.notFound).toggleClass( 'is-visible', $(element.row).not( '.is-hidden' ).length == 0 );
        },
        /**
         * Upload File
         * @param  {[type]} event [description]
         * @return {[type]}       [description]
         */
        uploadFile : function(event){
            event.preventDefault();
            var self = $(this);
            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: self.data('uploader_title'),
                button: {
                    text: self.data('uploader_button_text'),
                },
                multiple: false
            });
            file_frame.on('select', function () {
                attachment = file_frame.state().get('selection').first().toJSON();
                self.prev('.funkopt-url').val(attachment.url);
            });
            // Finally, open the modal
            file_frame.open();
        }
    };

    FunkmoSettings.init();

})(jQuery);