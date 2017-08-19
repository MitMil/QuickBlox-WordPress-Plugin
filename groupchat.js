var params, token, dialogId, dialogJid, login_id, quickblox_password, user_detail, message_timestamp;
jQuery(document).ready(function() {
	// Web SDK initialization
	QB.init(QBAPP.appID, QBAPP.authKey, QBAPP.authSecret);
	dialogId = QBAPP.publicRoomId;
	dialogJid = QBAPP.publicRoomJid;	
	// QuickBlox session creation
	QB.createSession(function(err, result) {
		if (err) {
			console.log(err.detail);
		} else {
			token = result.token;
			jQuery('#loginForm form').hide();
			jQuery('.tooltip-title').tooltip();
			login();
			jQuery('.sendMessage').click(sendmessage);			
			jQuery('#logout').click(logout);
			changeInputFileBehavior();
			quickblox_password = $("#password").val();
		}
	});
});
function login(event) {
	var login_user= {
	      login: $("#userName").val(),
	      password: $("#password").val()
	    };
	// check if user did not leave the empty field
	if (trim(login_user.login)) {
	    QB.login(login_user, function(loginErr, loginUser){
	          if(loginErr) {
	              /** Login failed, trying to create account */
	              QB.users.create(login_user, function (createErr, createUser) {
	                  if (createErr) {
	                      //alert(createErr);
	                  } else {
	                      QB.login(login_user, function (reloginErr, reloginUser) {
	                          if (reloginErr) {
	                            //alert(reloginErr);
	                          } else {
	                            login_id = reloginUser.id; 
	                            loginSuccess(reloginUser);
	                          }
	                      });
	                  }
	              });
	          } else {
	          	login_id = loginUser.id;
	            console.log("loginUser"+loginUser.id);
	              /** Update info */
	              //if(loginUser.user_tags !== login_user.tag_list || loginUser.full_name !== login_user.full_name) {
	                  // QB.users.update(loginUser.id, {
	                  //     'full_name': login_user.full_name,
	                  //     'tag_list': login_user.tag_list
	                  // }, function(updateError, updateUser) {
	                  //     if(updateError) {
	                  //         alert(updateError);
	                  //     } else {
	                  //         loginSuccess(updateUser);
	                  //     }
	                  // });
	             // } else {
	                  loginSuccess(loginUser);
	              //}
	          }
	      });
		}
}
function loginSuccess(user) {
	$('#wrap').show();
	$('.loader-block').show();
	QB.chat.connect({userId: user.id, password: quickblox_password}, function(err, roster) {
		if (err) {
			$('.loader-block').hide();
			alert('can not connect to quickblox');
		    console.log(err);
		} else {
			user_detail = user;
			jQuery('.chat input:text').attr('id', user.id);
			jQuery("input[type=file]").attr('id', 'file_'+user.id);
			getmessage(user);
		}
	});
}
function logout() {
	QB.chat.muc.leave(dialogJid, function() {
		console.log("Joined dialog " + dialogId);
	});
	alert('You are Logout from chat Now You will not Get messages');
}
function getmessage(user , scrollTodown='' , message_timestamp_scroll='') {
	$('.loader-block').hide();
	var height_msg = 0;
	//jQuery('#wrap').show();
	var params, date_sent_show;
	//console.log('dialogId'+dialogId);
	if(message_timestamp_scroll) {
		params = {chat_dialog_id: dialogId, sort_desc: 'date_sent', limit: 15, skip: 0 , "date_sent": {"lt": message_timestamp_scroll}};
	} else {
		params = {chat_dialog_id: dialogId, sort_desc: 'date_sent', limit: 10, skip: 0};
	}
	//console.log(JSON.stringify(params)+"====="+'token'+token)
	QB.chat.message.list(params, function(err, messages) {
		if (messages) {
			var html, selector = jQuery('.chat .messages');
			//console.log(JSON.stringify(messages)+'===');
			_.each(messages.items, function( i, val ) {
				date_sent_show = getTime(i.date_sent * 1000);	
				html = '<section class="message '+i.sender_id+'">';	
				var name = getusername(i.sender_id);
				var s_id = i.sender_id+"_name";
				html += '<div class="inner-msg"><header><div class="name-block"><b class="'+s_id+'"></b></div>';
				html += '</header>';
				if (i.attachments) {
					if(i.attachments.length > 0) {
						var fileId , qbSessionToken , privateUrl;
						if(i.attachments[0].url) {
							var str1 = i.attachments[0].url;
							var str2 = "?token";
							if(str1.indexOf(str2) != -1){
								privateUrl = i.attachments[0].url.split('?token=')[0]+"?token=" + token;
							} else {
								privateUrl = i.attachments[0].url+"?token=" + token;
							}
						} else {
							fileId = i.attachments[0].id;
							qbSessionToken = token;
							privateUrl = "https://api.quickblox.com/blobs/" + fileId + "?token=" + qbSessionToken;
						}
						if(i.message) {
							html += '<div class="message-description">' + i.message + '</div>';
						}
				      	html += '<div class="message-description">'+"<a href="+privateUrl+" target='_blank'><img src='" + privateUrl + "' alt='Image'  class='img-responsive'/></a>"+'</div>';
					} else {
						html += '<div class="message-description">' + i.message + '</div>';
					}
				}
				html += '<div class="time-block"><div class="time">' + date_sent_show + '</div></div></div></section>';

				selector.prepend(html);

				if(user.id === i.sender_id) {
					selector.find('.'+i.sender_id).addClass('white');
			  	} else {
			  		selector.find('.'+i.sender_id).addClass('receive');
			  	}
				if(scrollTodown) {
					$('.panel-body').scrollTop(50);
				} else {
					height_msg = height_msg+$('.messages').height();
					$('.panel-body').animate({scrollTop: height_msg});
					if($('.messages').height() > 400) {
						jQuery('.panel-body').css('overflow-y','scroll');
					}
				}
				message_timestamp = i.date_sent;
			});
		} else {
			jQuery('.panel-body').css('overflow-y','hidden');
			console.log(err);
		}
	});
	JoinGroup();
}
function JoinGroup(){
	console.log('======join=====');
	QB.chat.muc.join(dialogJid, function(resultStanza) {
		var joined = true;
			for (var i = 0; i < resultStanza.childNodes.length; i++) {
			var elItem = resultStanza.childNodes.item(i);
			if (elItem.tagName === 'error'){
			  joined = false; 
			}
		}
	});
	QB.chat.onMessageListener = onMessage;
}
function sendmessage(event) {
	console.log('======send=====');
	event.preventDefault();
	text = jQuery('.chat input:text').val();
	if(jQuery('#'+login_id).val('').length > 0) {
		jQuery('#'+login_id).val('');
	}
	var inputFile = $("input[type=file]")[0].files[0];
 	if(inputFile) {
		var params = {name: inputFile.name, file: inputFile, type: inputFile.type, size: inputFile.size, 'public': true};
		QB.content.createAndUpload(params, function(err, response){
		    if (err) {
		        console.log(err);
		    } else {
		    	console.log(JSON.stringify(response)+'response');
		        var uploadedFile = response;
		        var uploadedFileId = response.uid;
		        var msg_attachment = {
						type: 'groupchat',
						body: (trim(text)) ? text : '',
						extension: {
						save_to_history: 1,
			    	}
			    };
			    var url = "https://api.quickblox.com/blobs/" + uploadedFileId;//+"?token=" +token;
			    msg_attachment["extension"]["attachments"] = [{url: url, id: uploadedFileId, type: 'photo'}];
	  			QB.chat.send(dialogJid, msg_attachment);
	  			text = '';
	  			inputFile = '';
		    }
		});
 	} else {
 		if(trim(text)) {
			var msg = {
				type: 'groupchat',
				body: text,
				extension: {
					save_to_history: 1,
				},
				markable:1
			};
	 		QB.chat.send(dialogJid, msg);
			text = '';
 		}		
 	}
	QB.chat.onMessageListener = onMessage;
	jQuery('input:file').val('');
  
}
function onMessage(userId, message) {
		var html, selector = jQuery('.chat .messages');
		console.log(JSON.stringify(message)+'onMessage');
	  	QB.users.get(userId, function(err, result) {
			if (result) {
				if(login_id===result.id) {
					jQuery('#'+login_id).focus().val('');
				}
			  	var date_sent_show ;
		  		if(message.body || (message.extension.attachments.length > 0)) {
		  			date_sent_show = getTime(message.extension.date_sent * 1000);
					html = '<section class="message '+result.id+'">';
					html += '<div class="inner-msg"><header><div class="name-block"><b>' + result.login+ '</b></div>';
					html += '</header>';	
				  	if (message.extension.hasOwnProperty("attachments")) {
					    if(message.extension.attachments.length > 0) {
					    	var fileId , qbSessionToken , privateUrl;
							if(message.extension.attachments[0].url) {
								var str1 = message.extension.attachments[0].url;
								var str2 = "?token=";
								if(str1.indexOf(str2) != -1){
									privateUrl = message.extension.attachments[0].url.split('?token=')[0]+"?token=" + token;
								} else {
									privateUrl = message.extension.attachments[0].url+"?token=" + token;
								}
							} else {
								fileId = message.extension.attachments[0].id;
								qbSessionToken = token;
								privateUrl = "https://api.quickblox.com/blobs/" + fileId + "?token=" + qbSessionToken;
							}
							console.log('privateUrl==='+privateUrl);
							if(message.body) {
								html += '<div class="message-description">' + message.body + '</div>';
							}
							html += '<div class="message-description">'+"<a href="+privateUrl+" target='_blank'><img src='" + privateUrl + "' alt='Image' class='img-responsive'/></a>"+'</div>';
					    }
					    if(login_id===result.id) {
					    	jQuery('#file_'+login_id).val('');
					    	jQuery('#file_'+login_id).val('').length = 0;
							closeFile();
					    }
					} else {
						html += '<div class="message-description">' + message.body + '</div>';
					}
					html += '<div class="time-block"><div class="time">' + date_sent_show + '</div></div></div></section>';
					selector.append(html);
					if(selector.find('.'+result.id)) {
						if(login_id === result.id) {
							selector.find('.'+login_id).addClass('white');
					  	} else {
					  		selector.find('.'+result.id).addClass('receive');
					  	}
					}
					if($('.messages').height() > 600) {
						$('.panel-body').css('overflow-y','scroll');
						$('.panel-body').scrollTop($('.messages').height());
					} else {
						$('.panel-body').scrollTop($('.messages').height());
					}
				}
			} else  {
			  	console.log(err);
			    // error
			}
		});
}
jQuery('.uploader').on("click",".close",function(event){
	jQuery('.attach').hide();
	jQuery('#file_'+login_id).val('');
	jQuery('#file_'+login_id).val('').length = 0;
});
var lastScrollTop = 0;
var scrollTodown = false;
jQuery('.panel-body').scroll(function(event){
   var st = $(this).scrollTop();
   if(st==0) {
   		scrollTodown = true;
   		var load_more = jQuery('.chat .messages section').length;
   		if(load_more >=10) {
			if(message_timestamp) {
				check_message_availability(message_timestamp);
			}	   		
   		}
   } else {
   		if(jQuery('.chat .messages').find('.remove_class')) {
   			scrollTodown = false;
   			jQuery('.remove_class').remove();
   		}
   }
   lastScrollTop = st;
});
function check_message_availability(message_timestamp_avail){
	//console.log('message_timestamp_avail'+message_timestamp_avail);
	var params = {chat_dialog_id: dialogId, sort_desc: 'date_sent', limit: 15, skip: 0 , "date_sent": {"lt": message_timestamp_avail}};
	QB.chat.message.list(params, function(err, messages) {
		if (messages) {
			var msg_lenght = messages.items.length;
			if(msg_lenght > 0) {
				if(jQuery('.chat .messages').find('.remove_class')) {
		   			jQuery('.remove_class').remove();
		   			msg_lenght = 0;
		   		}
				//console.log(msg_lenght+'msg_lenght');
				jQuery('.chat .messages').first().prepend('<section class="remove_class"><div class="message loading_class"><div id="loadMore">Load more</div></div></section>');
				$('#loadMore').click(function () {
	    			jQuery('.remove_class').remove();
					getmessage(user_detail,scrollTodown,message_timestamp_avail);
	    		});
			}
		}
	});
}
function getusername(sender_id) {
	var params_new = { page: '1', per_page: '100'};
	QB.users.listUsers(params_new, function(err, users){
		if (users) {
			jQuery.each( users.items, function( i, val ) {
				if(sender_id === val.user.id) {
					var id = "."+sender_id+"_name";
					jQuery(id).text(val.user.login);
				}
			});
		}
	});
}