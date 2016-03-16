
(function($){
  $.fn.outside = function(ename, cb){
      return this.each(function(){
          var $this = $(this),
              self = this;

          $(document).bind(ename, function tempo(e){
              if(e.target !== self && !$.contains(self, e.target)){
                  cb.apply(self, [e]);
                  if(!self.parentNode) $(document.body).unbind(ename, tempo);
              }
          });
      });
  };
}(jQuery));
var gisTree = {
		loadTree : function() {
	        $.ajax({
	            url:window.base_url +'/jsonTree',
	            type:'POST',
	            data:{ isVisibleLayer:$('.data').attr('showall'),
                        user_id:$('.data').attr('user_id')
                },
	            async:false,
	            cache:false,
	            success: function(data) {
                    reloadPage(data);
                    gisTree.bindToJsTree(data);
	                return data;
	            }
	        });
	    },
	    bindToJsTree: function(data){
	    	var $tree=$('.data');
	        if($('.data').jstree()!=undefined)
	        {
	            $('.data').jstree().destroy();
	        }
	        $tree.jstree({
				'core' : {
					'force_text' : true,
					'data' : data,
                    'check_callback' : function (op, node, par, pos, more) {
                       if(op == 'rename_node'){

                    	   var token = $('input[name=_token]').val();
                           var sent=true;
                           if(node.text==pos) {
                               sent=false;
                           }
                           if(sent){
                               $.ajax({
                                   url: window.base_url + '/admin/folders/update-layer/'+node.id,
                                   data: {
                                       folderId: node.id,
                                       name : pos.trim()
                                   },
                                   type:  'post',
                                   success: function( data ){
                                       //reloadPage(data);

                                       var errorMessage = buildMessage(data.message);
                                       var title = Lang.get('common.info_title');
                                       fancyMessage(errorMessage,title,function(){
                                           if(data.code == 401) {
                                               debugger;
                                               window.location.href = window.base_url + '/login';
                                           }
                                       	if(data.code == 403){
                                               window.location.href = permission_denined_url;
                                           }
                                       	gisTree.loadTree();
                                       });
                                   }
                               })
                           }
                       }else if(op == 'delete_node'){
                    	    var totalFolderChecked = gisTree.getTotalFolderSelect();
               				var totalLayerChecked = gisTree.getTotalLayerSelect();
               				var isFolderSelected = true;
               				
               				if (totalFolderChecked == 0 && totalLayerChecked == 0) {
               					fancyAlert(Lang.get('common.folder_edit_ids_required'),
               							Lang.get('common.error_title'));
               					return false;
               				}else if(totalLayerChecked > 0){
               					isFolderSelected = false;
               					var selectedIds = gisTree.getLayerSelected('all');
               				}else
               					var selectedIds = gisTree.getFolderSelected('all');
                    	   
               				bootbox.dialog({
                               message : Lang.get('common.confirm_action'),
                               title : Lang.get('common.info_title'),
                               buttons : {
                                   danger : {
                                       label : Lang.get('common.yes'),
                                       className : "btn-primary",
                                       callback : function(){
											var token = $('input[name=_token]').val();
											   $.ajax({
											       url: window.base_url + '/admin/folders/delete-folders',
											       data: {
											           folderIds : selectedIds,
											           isFolderSelected : isFolderSelected
											       },
											       type:  'post',
											       success: function( data ){
                                                       reloadPage(data);
                                                       if(data.code == 401) {
					                                        window.location.href = window.base_url + '/login';
					                                    }
					                                    var errorMessage = buildMessage(data.message);
                                                       var title = Lang.get('common.info_title');
                                                       fancyMessage(errorMessage,title,function(){
					                                    	if(data.code == 403){
					                                            window.location.href = permission_denined_url;
					                                        }
					                                    	gisTree.loadTree();
					                                    });
											       }
										       })
										   }
                                   },
                                   success : {
                                       label : Lang.get('common.no'),
                                       className : "btn-primary",
                                       callback: function(){
                                           $('.data').jstree('deselect_all');
                                           $('.data').jstree().refresh();
                                       }
                                   }
                               }
                           });
                    	   return false;
                       }
                    },
                    multiple : false
                    
				},
				"plugins" : [ "dnd", "contextmenu", "types","state" ],
                "contextmenu":
                {
                    "items": function ($node) {
                        var tmp = $.jstree.defaults.contextmenu.items();
                        delete tmp.create.action;
                        delete tmp.fertilizer_main_function;
                        delete tmp.fertilizer_2;
                        delete tmp.fertilizer_pdf;
                        delete tmp.fertilizer_buy;
                        tmp.remove.label=Lang.get('common.folder_button_delete');
                        tmp.rename.label=Lang.get('common.folder_button_rename');
                        if(isAdmin) {
                            tmp.create.label = Lang.get('common.folder_button_edit');
                            tmp.create.action = function () {
                                $.fancybox([{
                                    href: window.base_url + '/admin/folders/' + $node.id + '/edit',
                                    type: 'ajax',
                                    helpers: {
                                        overlay: {
                                            closeClick: false
                                        }
                                    }
                                }], {
                                    afterLoad: function (data) {
                                        try {
                                            var json = $.parseJSON(data.content);
                                            fancyAlertAndLoadPage(json.message, Lang.get('common.error_title'));
                                            top.$.fancybox.close();
                                            $('.data').jstree().refresh();
                                            return false;
                                        } catch (err) {

                                        }
                                    }
                                });
                            };
                        }
                        else delete tmp.create;
                        delete tmp.ccp.submenu;
                        delete tmp.ccp.action;
                        tmp.ccp.label= Lang.get('common.folder_button_terain_create');
                        tmp.ccp.action =function() {
                                    $.fancybox([ {
                                        href : window.base_url + '/admin/folders/create-layer',
                                        type : 'ajax',
                                        helpers : {
                                            overlay : {
                                                closeClick : false
                                            }
                                            // Disable click outside event
                                        }
                                    } ], {
                                        afterLoad : function(data) {
                                            try {
                                                reloadPage(data);

                                                var json = $.parseJSON(data.content);
                                                fancyAlert(json.message,Lang.get('common.error_title'));
                                                top.$.fancybox.close();
                                                return false;
                                            } catch (err) {

                                            }
                                        }
                                    });
                                };
                        if(this.get_type($node.parent)=="folder_bin"){
                            tmp.layer_restore.label= Lang.get('common.mouse_right_click_layer_restore');
                            tmp.layer_restore.action =function() {
                                $.ajax({
                                    url: window.base_url + '/folders/layer-restore/'+$node.id,
                                    type: "GET",
                                    success: function (data) {
                                        if (data.code != 200) {
                                            fancyMessage(data.message, Lang.get('common.error_title'), function () {
                                                top.location.reload();
                                                return true;
                                            });
                                        }
                                        else if(data.code==200){
                                            gisTree.loadTree();
                                        }
                                    }
                                });
                            }
                        }
                        else delete tmp.layer_restore;
                        if(($node.type.indexOf('folder') >(-1))&& ($node.type.indexOf('terrain')==(-1))){
                            delete tmp.ccp;
                        }
                        if($node.type.indexOf('layer') >(-1)) {
                            delete tmp.create;
                            delete tmp.ccp;
                            if($node.type=='layer_terrain')
                            delete tmp.remove;
                        }
                        return tmp;
                    }
                },
                "types" : {
                    "#" : {
                        "max_depth" : 2,
                        "valid_children" : [ "folder_admin","folder_fertility","folder_fertilizer","folder_terrain","folder_bin"]
                    },
                    "folder_admin" : {
                        "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                        "max_depth" : 1,
                        "is_draggble" : false
                    },
                    "folder_fertility" : {
                        "valid_children" : [ "layer_fertility","layer_fertility_hidden","layer_admin","layer_admin_hidden"],
                        "max_depth" : 1,
                        "is_draggble" : false
                    },
                    "folder_fertilizer" : {
                        "valid_children" : [ "layer_fertilizer","layer_fertilizer_hidden"],
                        "max_depth" : 1,
                        "is_draggble" : false
                    },
                    "folder_terrain" : {
                        "valid_children" : [ "layer_terrain","layer_terrain_hidden"],
                        "max_depth" : 1,
                        "is_draggble" : false
                    },
                    "folder_bin" : {
                        "valid_children" : ["layer_fertility","layer_fertilizer","layer_fertility_hidden","layer_fertilizer_hidden","layer_admin","layer_admin_hidden"],
                        "max_depth" : 1,
                        "is_draggble" : false
                    },
                    "layer_admin" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_admin_hidden" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_fertility" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },"layer_fertility_hidden" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_fertilizer" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_fertilizer_hidden" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_terrain" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    },
                    "layer_terrain_hidden" : {
                        "valid_children" : "none",
                        "max_children" : 0,
                        "max_depth" : 0,
                        "icon" : "jstree-file"
                    }

                }
			}).bind('move_node.jstree', function(e, data) {
				var target = e.delegateTarget;
				var anchor_id = $(target).attr('aria-activedescendant')+'_anchor';
        		var li_target = $('li[aria-labelledby='+anchor_id+']');
        		var token = $('input[name=_token]').val();
        		var is_folder = data.node.parent == '#' ? true : false;
            	new_order =  li_target.attr('data-order'); 
        		
        		var url =  window.base_url + '/admin/folders/'+data.node.id
				var requestData = {order_number: new_order,  sortAble : true};
        		var confirm = true;
        		
        		if(!is_folder){
            		if(data.parent != data.old_parent){
                        if((data.node.type.indexOf("fertility")>1)&&($tree.jstree().get_node(data.node.parent).type=='folder_admin')){
                            fancyAlert(Lang.get('common.can_not_move_from_folder_fertility_to_folder_admin'),Lang.get('common.error_title'),Lang.get('common.button_alert_ok'));
                            $tree.jstree('refresh');
                            confirm = false;
                        }
            			url = window.base_url + '/admin/folders/change-folder';
            			requestData = {layerId : data.node.id,folderId : data.parent};
            		}else{
            			if(data.old_position == data.position)
            				confirm = false;
            		}
            	}else{
            		if(li_target.attr('aria-level') > 1){
						new_order =  li_target.parents('li[aria-level=1]').attr('data-order');
					}
            		if(data.old_position == data.position)
            			confirm = false;
            	}
        		
        		
        		if(confirm){
        			bootbox.dialog({
	                    message : Lang.get('common.confirm_action'),
	                    title : Lang.get('common.info_title'),
	                    buttons : {
	                        danger : {
	                            label : Lang.get('common.yes'),
	                            className : "btn-primary",
	                            callback : function(){
                        			$.ajax({
            	    		            url: url,
            	    		            data: requestData,
            	    		            type:  'put',
            	    		            beforeSend : function(request){
            	    		                return request.setRequestHeader('X-CSRF-Token', token);
            	    		            },
            	    		            success: function( data ){
                                            reloadPage(data);

                                            if(data.code == 401) {
                                                window.location.href = window.base_url + '/login';
                                            }
                                            else if(data.code==200){
                                                gisTree.loadTree();
                                            }
                                            else {
                                                var errorMessage = buildMessage(data.message);
                                                fancyMessage(errorMessage,Lang.get('common.info_title'),function(){
                                                    if (data.code == 403) {
                                                        window.location.href = permission_denined_url;
                                                    }
                                                    gisTree.loadTree();
                                                });
                                            }
            	    		            }
            	    		        })             
	                            }
	                        },
                            success : {
                                label : Lang.get('common.no'),
                                className : "btn-primary",
                                callback: function(){
                                    $tree.jstree().refresh();
                                }
                            }
	                    }
	                });
        		}
			})
            $tree.on("keydown", ".jstree-rename-input", function (e) {
                $('.jstree-rename-input').attr('maxLength', 100);
            })
	},
	getTotalFolderSelect : function() {
		var total_checked = 0;
		$('ul.jstree-container-ul > li[aria-level=1]').each(function() {
			if ($(this).attr('aria-selected') == "true")
				total_checked += 1;
		});

		return total_checked;
	},
	getTotalLayerSelect : function(){
		var total_checked = 0;
		$('ul.jstree-children > li[aria-level=2]').each(function() {
			if ($(this).attr('aria-selected') == "true")
				total_checked += 1;
		});

		return total_checked;
	},
	getFolderSelected : function(all) {
		var folders = $('ul.jstree-container-ul > li[aria-selected=' + true + '] ');
		if(typeof all === 'undefined'){
			return folders.attr('id');
		}
		var folderIds = [];
		folders.each(function(){
			folderIds.push($(this).attr('id'));
		})
		return folderIds;
	},
	getLayerSelected : function(all) {
		var folders = $('ul.jstree-children > li[aria-selected=' + true + '] ');
		if(typeof all === 'undefined'){
			return folders.attr('id');
		}
		var folderIds = [];
		folders.each(function(){
			folderIds.push($(this).attr('id'));
		});
		return folderIds;
	},
	refreshTree : function(element) {
		data.instance.refresh();
	}

};
