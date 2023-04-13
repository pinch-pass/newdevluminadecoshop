var data = {controller: 'AdminSendpulsePreExport', exporttime: true, ajax: true};

var ajax_url = "/modules/sendpulse/ajax.php";
var modal = false;
var stop_it = false;

$(document).ready(function(){
	modal = $('#sendpulse_modal').modal({
		backdrop: 'static',
		keyboard: false,
		closable: true,
		show: false,
	});
	if($("#sendpulse_import").length>0){
		$.ajax({
			type: "POST",
			url: ajax_url, 
			timeout: 0,
			data: {method: 'preimport'},
			dataType: "json",
			success: function(data){
				if(data.success && data.data){
					$("#sendpulse_import info").html("");
					$.each(data.data, function(index, val){
						writeTR("#sendpulse_import", val);
					});
					$("#sendpulse_import .info_").html("");
				}else{
					$("#sendpulse_import .info_").html(data.message);
				}
			},
			error: function(x){
				console.log(x);
			}
		})
	}
	$(document).on("click", ".cancel_button", function(){
		stop_it = true;
		$("#sendpulse-categories input[type=checkbox].contacts_exist").removeAttr("disabled");
		return false;
	});
	
});


var progressBar = false;

function importAllFromSendpulse(form){
	progressBar = modal.find(".progress-bar");
	var ids = [],
 		els = form.find(".category");
 	if(!els.length){
 		return false;
 	}
 	form.find("input[type=checkbox]").attr("disabled", "disabled");	
	$.each(els, function(){
		category_id = $(this).val();
		count = $(this).closest("tr").data("category-count");
		name = $(this).closest("tr").data("category-name");
		ids.push({category_id: category_id, count: count, name: name});
	});
	
	progress.init(progressBar, ids);
	modal.modal("show")
	sendCategories(ajax_url, ids, 'import');
	return false;
}


function exportAllToSendpulse(form){
	progressBar = modal.find(".progress-bar");
	var ids = [],
 		els = form.find(".category");
 	if(!els.length){
 		return false;
 	}	
 	form.find("input[type=checkbox]").attr("disabled", "disabled");	
	$.each(els, function(){
		category_id = $(this).val();
		count = $(this).closest("tr").data("category-count");
		name = $(this).closest("tr").data("category-name");
		ids.push({category_id: category_id, count: count, name: name});
	});
	progress.init(progressBar, ids);
	modal.modal("show")
	sendCategories(ajax_url, ids, 'export');
	return false;
}

function exportCheckedToSendpulse(but){
	form = but.closest("#sendpulse_export").find("#sendpulse-categories");
	progressBar = modal.find(".progress-bar");
	var ids = [],
 		els = form.find("input[type=checkbox]:checked");
 	if(!els.length){
 		return false;
 	}	
 	form.find("input[type=checkbox]").attr("disabled", "disabled");	
	$.each(els, function(){
		category_id = $(this).val();
		count = $(this).closest("tr").data("category-count");
		name = $(this).closest("tr").data("category-name");
		ids.push({category_id: category_id, count: count, name: name});
	});
	progress.init(progressBar, ids);
	modal.modal("show")
	sendCategories(ajax_url, ids, 'export');
	return false;
}


function importCheckedFromSendpulse(but){
	form = $("#sendpulse-categories");
	progressBar = modal.find(".progress-bar");
	var ids = [],
 		els = form.find("input[type=checkbox]:checked");
 	if(!els.length){
 		return false;
 	}	
 	
 	
 	form.find("input[type=checkbox]").attr("disabled", "disabled");	
	$.each(els, function(){
		category_id = $(this).val();
		count = $(this).closest("tr").data("category-count");
		name = $(this).closest("tr").data("category-name");
		ids.push({category_id: category_id, count: count, name: name});
	});
	progress.init(progressBar, ids);
	modal.modal("show")
	sendCategories(ajax_url, ids, 'import');
	return false;
}

function sendCategories(url, categories, type){
	if(categories.length>0){
		current = categories.pop();
		page = 0;
		pages = Math.ceil(current.count/100);
		sendCategoryPage(url, current, page, pages, categories, type);
	}else{
		modal.find(".current_category").html(messages['complete']+". "+messages['errors']+": "+modal.find(".logs span.error").length+". "+messages['records']+": "+progress.total_ids);
		$("#sendpulse-categories input[type=checkbox].contacts_exist").removeAttr("disabled");
		stop_it = false;
	}
}


function sendCategoryPage(url, category, page, pages, categories, type){
	if(page===0){
		offset = 0;
	}else{
		offset = page*100;
	}
	modal.find(".current_category").html(category.name+"...");
	$.when(
		$.ajax({
			type: "POST",
			url: url, 
			timeout: 0,
			data: {category_id: category.category_id, offset: offset, count: category.count, method: type},
			dataType: "json",
			error: function(x){
				console.log(x);
				if(stop_it){
					stop_it = false;
					return false;
				}
				//sendCategoryPage(url, category, page, pages, categories, type);
				if($(x.responseText).find(".exception_message").lenght){
					alert("ERROR "+$(x.responseText).find(".exception_message").html());
				}else{
					alert("ERROR "+x.responseText);
				}
				$("#sendpulse-categories input[type=checkbox].contacts_exist").removeAttr("disabled");
				console.error("Error 500/504");
				return false;
			}
		})
	).then(function(data, textStatus, jqXHR){
		console.log(data);
		if(!data.success && !data.try_again && !data.wrong_name){
			progress.moveProgress(data.message, true, false);
			return false;
		}
		
		if(data.errors){
			modal.find(".logs").append(data.errors);
		}
		
		if(data.try_again && page!=pages-1 && !stop_it){
			console.error("Try again after 1000 ms... page: "+page+", cagegory: "+category);
			setTimeout(function(){
				sendCategoryPage(url, category, page, pages, categories, type);
			},1000);
		}else{
			progress.moveProgress(category.name, false, false);
			console.log(page+"!="+pages);
			if(page!=pages-1 && !stop_it){
				sendCategoryPage(url, category, (page+1), pages, categories, type);
			}else{
				console.log("finish cat "+category.category_id);
				if(stop_it){
					console.log("aborted...");
					stop_it = false;
					return false;
				}else{
					sendCategories(url, categories, type);
				}
			}
		}
	});    	
};



var progress = {
	progressBar: false,
	categories: false,
	step: 0,
	per_step: 0,
	current: 0,
	maximum: 0,
	dialog: false,
	init: function(progressBar, categories){
		modal.find(".cancel_button").show();
		modal.find(".close_modal").hide();
		
		
		
		modal.find(".logs").html("");
		stop_it = false;
		this.step = 0;
		this.progressBar = progressBar;
		this.progressBar.removeClass("progress-bar-success");
		this.current = 0;
		this.categories = categories;
		this.total_ids = 0;
		this.total = this.calculateMaxSteps(this.categories);
		this.per_step = (100/this.total);
		modal.find(".current_category").html(this.categories[0].name+"...");
		modal.find(".current_page").html("0");
		modal.find(".total").html(this.total_ids);
		
		this.clear();
	},
	calculateMaxSteps: function(categories){
		cats_count = categories.length;
		cats_pages = 0;
		for(i=0; i!=categories.length; i++){
			if(categories[i].count>100){
				cats_pages = cats_pages+(Math.ceil(categories[i].count/100));
				cats_count--;
			}
			this.total_ids += categories[i].count;
		}
		cats_count = cats_count+cats_pages;
		return cats_count;	
	},
	moveProgress: function(name, error, temp__){
		this.step++;
		if(error){
			this.progressBar.addClass("progress-bar-danger");
			modal.find(".current_category").html(name);
			return false;
		}
		this.current += this.per_step;
		this.progressBar.css('width', this.current+"%");

		if(this.total_ids < 100){
			//alert(this.total+"<100");
			modal.find(".current_page").html(this.total_ids);
		}else{
			//alert(this.step*100)
			modal.find(".current_page").html(this.step*100);
		}
		percent = Math.ceil(this.step*100/this.total);

		this.progressBar.find('span').html(percent+"%"); //this.step+"/"+this.total);
		if(percent==100){
			this.progressBar.addClass("progress-bar-success");
			modal.find(".cancel_button").hide();
			modal.find(".close_modal").show();
		}
		this.progressBar.find(".current_category").html(name+"...");
	},
	clear: function(){
		progressBar.css('width', '0%');
		progressBar.find('span').html('');
		progressBar.removeClass('progress-bar-danger');		
	},
	stop_it: function(){

	}
	
};


function writeTR(parent, data){
	tr_data = "data-category-id="+data.id+" data-category-count="+data.all_email_qty+" data-category-name='"+data.name+"'";
	
	check_class = data.all_email_qty>0?"":"disabled";
	check_class2 = data.all_email_qty>0?"contacts_exist":"";
	
	check = "<td class='center'><input "+check_class+" class='category "+check_class2+"' type='checkbox' name='category[]' value='"+data.id+"'></td>";
	id = "<td class='center'>"+data.id+"</td>";
	name = "<td>"+data.name+"</td>";
	count = "<td class='center'>"+data.all_email_qty+"</td>";
	$(parent).find("tbody").append("<tr "+tr_data+" >"+check+id+name+count+"</tr>");
}


function checkAll(but){
	form = $("#sendpulse-categories");
 	form.find("input[type=checkbox]:not(:disabled)").attr("checked", "checked");
 	return false;
}

function unCheckAll(but){
	form = $("#sendpulse-categories");
 	form.find("input[type=checkbox]:not(:disabled)").removeAttr("checked");
 	return false;
}





