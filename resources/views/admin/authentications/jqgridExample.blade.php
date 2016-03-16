<!DOCTYPE html>
<html lang="en">
<head>
    <!-- The jQuery library is a prerequisite for all jqSuite products -->
    <script src="{{url('/jqgrid/jquery.min.js')}}"></script>
    <script src="{{url('/js/jquery-ui.min.js')}}"></script>
    <!-- This is the Javascript file of jqGrid -->   
    <script src="{{url('/jqgrid/jquery.jqGrid.min.js')}}"></script>	
    <!-- This is the localization file of the grid controlling messages, labels, etc.
    <!-- We support more than 40 localizations -->
    <script src="{{url('/jqgrid/grid.locale-en.js')}}"></script>
    <!-- A link to a jQuery UI ThemeRoller theme, more than 22 built-in and many more custom -->
	<link href="{{ asset('/jqgrid/jquery-ui.css') }}" rel="stylesheet">
    <!-- The link to the CSS that the grid needs -->
	<link href="{{ asset('/jqgrid/ui.jqgrid.css') }}" rel="stylesheet">
    <meta charset="utf-8" />
    <title>jqGrid Loading Data - Virtual mode - paging with scrollbar</title>
    
    

<style>
  .custom-combobox {
    position: relative;

  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
  }

  </style>
  <script>
  (function( $ ) {
      $.widget( "custom.combobox", {
        _create: function() {
          this.wrapper = $( "<span>" )
            .addClass( "custom-combobox" )
            .insertAfter( this.element );

          this.element.hide();
          this._createAutocomplete();
          this._createShowAllButton();
        },

        _createAutocomplete: function() {
          var selected = this.element.children( ":selected" ),
            value = selected.val() ? selected.text() : "";

          this.input = $( "<input>" )
            .appendTo( this.wrapper )
            .val( value )
            .attr( "title", "" )
            .addClass( "custom-combobox-input form-control ui-corner-left" )
            .autocomplete({
              delay: 0,
              minLength: 0,
              source: $.proxy( this, "_source" )
            })
             ;

          this._on( this.input, {
            autocompleteselect: function( event, ui ) {
              ui.item.option.selected = true;
              this._trigger( "select", event, {
                item: ui.item.option
              });
            },

            autocompletechange: "_removeIfInvalid"
          });
        },

        _createShowAllButton: function() {
          var input = this.input,
            wasOpen = false;

          $( "<a>" )
            .attr( "tabIndex", -1 )
            .appendTo( this.wrapper )
            .button({
              icons: {
                primary: "ui-icon-triangle-1-s"
              },
              text: false
            })
            .removeClass( "ui-corner-all" )
            .addClass( "custom-combobox-toggle ui-corner-right " )
            .mousedown(function() {
              wasOpen = input.autocomplete( "widget" ).is( ":visible" );
            })
            .click(function() {
              input.focus();

              // Close if already visible
              if ( wasOpen ) {
                return;
              }

              // Pass empty string as value to search for, displaying all results
              input.autocomplete( "search", "" );
            });
        },

        _source: function( request, response ) {
          var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
          response( this.element.children( "option" ).map(function() {
            var text = $( this ).text();
            if ( this.value && ( !request.term || matcher.test(text) ) )
              return {
                label: text,
                value: text,
                option: this
              };
          }) );
        },

        _removeIfInvalid: function( event, ui ) {

          // Selected an item, nothing to do
          if ( ui.item ) {
            return;
          }

          // Search for a match (case-insensitive)
          var value = this.input.val(),
            valueLowerCase = value.toLowerCase(),
            valid = false;
          this.element.children( "option" ).each(function() {
            if ( $( this ).text().toLowerCase() === valueLowerCase ) {
              this.selected = valid = true;
              return false;
            }
          });

          // Found a match, nothing to do
          if ( valid ) {
            return;
          }

          // Remove invalid value
          this.input
            .val( "" )

          this.element.val( "" );
          this._delay(function() {
            this.input.tooltip( "close" ).attr( "title", "" );
          }, 2500 );
          this.input.autocomplete( "instance" ).term = "";
        },

        _destroy: function() {
          this.wrapper.remove();
          this.element.show();
        }
      });
    })( jQuery );


  $(function() {
    $( "#combobox" ).combobox();

  });
  </script>
</head>
<body>

<div class="ui-widget form-control">
  <label>Your selection: </label>
  <select id="combobox" class="form-control">
    <option value="">Select one...</option>
    <option value="ActionScript">ActionScript</option>
    <option value="AppleScript">AppleScript</option>
    <option value="Asp">Asp</option>
    <option value="BASIC">BASIC</option>
    <option value="C">C</option>
    <option value="C++">C++</option>
    <option value="Clojure">Clojure</option>
    <option value="COBOL">COBOL</option>
    <option value="ColdFusion">ColdFusion</option>
    <option value="Erlang">Erlang</option>
    <option value="Fortran">Fortran</option>
    <option value="Groovy">Groovy</option>
    <option value="Haskell">Haskell</option>
    <option value="Java">Java</option>
    <option value="JavaScript">JavaScript</option>
    <option value="Lisp">Lisp</option>
    <option value="Perl">Perl</option>
    <option value="PHP">PHP</option>
    <option value="Python">Python</option>
    <option value="Ruby">Ruby</option>
    <option value="Scala">Scala</option>
    <option value="Scheme">Scheme</option>
  </select>
</div>
    <table id="jqGrid"></table>
    <div id="jqGridPager"></div>
    <button onclick="jqgridExample.refresh();">Search</button>
    <button onclick="jqgridExample.getParamSelect('jqGrid');">Get selected</button>
    <button onclick="jqgridExample.getCellValue('jqGrid','111','username');">Get username</button>
    <script src="{{url('/jqgrid/jqgridExample2.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function () {

        	jqgridExample.loadGridData();

        });

    </script>

    <!-- End of code related to code tabs -->
</body>
</html>