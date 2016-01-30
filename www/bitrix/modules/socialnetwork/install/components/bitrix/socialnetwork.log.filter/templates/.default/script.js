__logOnDateChange = function(sel)
{
	var bShowFrom=false, bShowTo=false, bShowHellip=false, bShowDays=false, bShowBr=false;

	if(sel.value == 'interval')
		bShowBr = bShowFrom = bShowTo = bShowHellip = true;
	else if(sel.value == 'before')
		bShowTo = true;
	else if(sel.value == 'after' || sel.value == 'exact')
		bShowFrom = true;
	else if(sel.value == 'days')
		bShowDays = true;

	BX('flt_date_from_span').style.display = (bShowFrom? '':'none');
	BX('flt_date_to_span').style.display = (bShowTo? '':'none');
	BX('flt_date_hellip_span').style.display = (bShowHellip? '':'none');
	BX('flt_date_day_span').style.display = (bShowDays? '':'none');
}

function onFilterGroupSelect(arGroups)
{
	if (arGroups[0])
	{
		BX('filter-field-user').value = '';
		document.forms["log_filter"]["flt_to_user_id"].value = 0;
		document.forms["log_filter"]["flt_group_id"].value = arGroups[0].id;
		BX.removeClass(BX("filter-field-group").parentNode.parentNode, "webform-field-textbox-empty");
	}
}

function onFilterCreatedBySelect(arUser)
{
	if (arUser.id)
	{
		document.forms["log_filter"]["flt_created_by_id"].value = arUser.id;
		document.forms["log_filter"]["filter-field-created-by"].value = arUser.name;
		BX.removeClass(BX("filter-field-created-by").parentNode.parentNode, "webform-field-textbox-empty");
		if (BX("flt_comments_cont"))
			BX("flt_comments_cont").style.display = "block";
	}
	else if(
		typeof arUser === "object"
		&& arUser.type != undefined
		&& arUser.type === "change"
	)
	{
		if (BX("flt_comments_cont"))
			BX("flt_comments_cont").style.display = "block";
	}
	else if (BX("flt_comments_cont"))
		BX("flt_comments_cont").style.display = "none";

	if (typeof filterCreatedByPopup === "object")
		filterCreatedByPopup.close();
}

function onFilterUserSelect(arUser)
{
	if (arUser.id)
	{
		BX('filter-field-group').value = '';
		document.forms["log_filter"]["flt_group_id"].value = 0;
		document.forms["log_filter"]["flt_to_user_id"].value = arUser.id;
		document.forms["log_filter"]["filter-field-user"].value = arUser.name;
		BX.removeClass(BX("filter-field-user").parentNode.parentNode, "webform-field-textbox-empty");
	}

	filterUserPopup.close();
}

function onFilterDestChangeTab(type)
{
	var type_hide;
	if (type != 'group')
	{
		type = 'user';
		type_hide = 'group';
		if (
			filterGroupsPopup !== undefined 
			&& filterGroupsPopup.popupWindow !== undefined
		)
		{
			filterGroupsPopup.popupWindow.close();
		}
	}
	else
	{
		type_hide = 'user';
		if (typeof filterUserPopup != 'undefined')
		{
			filterUserPopup.close();
		}
	}

	BX.removeClass(BX('filter-dest-' + type + '-tab'), 'webform-field-action-link');
	BX.addClass(BX('filter-dest-' + type_hide + '-tab'), 'webform-field-action-link');

	BX('filter-dest-' + type + '-block').style.display = 'inline-block';
	BX('filter-dest-' + type_hide + '-block').style.display = 'none';

	if (type != 'group')
	{
		BX("filter-field-user").focus();
		__SLFShowUseropup(BX("filter-field-user"));
	}
	else
	{
		BX("filter-field-group").focus();
		__SLFShowGroupsPopup();
	}
}

var filterPopup = false;

function ShowFilterPopup(bindElement, bIntranet)
{

	if (!filterPopup)
	{
		//BX.showWait(bindElement);
		BX.ajax.get(BX.message('sonetLFAjaxPath'), function(data) 
		{
			BX.closeWait(bindElement);

			filterPopup = new BX.PopupWindow(
				'bx_log_filter_popup',
				bindElement,
				{
					closeIcon : true,
					offsetTop: 5,
					autoHide: (!!bIntranet),
					zIndex : -100,
					className : 'sonet-log-filter-popup-window',
					events : { }
				}
			);

			var filter_block = BX.create('DIV', {html: BX.util.trim(data)});
			filterPopup.setContent(filter_block.firstChild);
			filterPopup.show();

			if (!!bIntranet)
			{
				BX.bind(BX("filter-field-created-by"), "click", function(e) {

					if(!e) e = window.event;

					filterCreatedByPopup = BX.PopupWindowManager.create("filter-created-by-popup", this.parentNode, {
						offsetTop : 1,
						autoHide : true,
						content : BX("FILTER_CREATEDBY_selector_content"),
						zIndex : 1200,
						buttons : [
							new BX.PopupWindowButton({
								text : BX.message("sonetLDialogClose"),
								className : "popup-window-button-accept",
								events : {
									click : function() {
										this.popupWindow.close();
									}
								}
							})
						]
					});

					if (filterCreatedByPopup.popupContainer.style.display != "block")
						filterCreatedByPopup.show();

					return BX.PreventDefault(e);
				});
			}			

			BX.bind(BX.findNextSibling(BX("filter-field-created-by"), {tagName : "a"}), "click", function(e){
				if(!e) e = window.event;

				BX("filter-field-created-by").value = "";
				BX("filter_field_createdby_hidden").value = "0";
				BX.addClass(BX("filter-field-created-by").parentNode.parentNode, "webform-field-textbox-empty");
				if (BX("flt_comments_cont"))
					BX("flt_comments_cont").style.display = "none";
				return BX.PreventDefault(e);
			});

			if (BX("filter-field-group"))
			{
				BX.bind(BX("filter-field-group"), "click", function(e) {
					if(!e) e = window.event;
					filterGroupsPopup.show();
					return BX.PreventDefault(e);
				});

				BX.bind(BX.findNextSibling(BX("filter-field-group"), {tagName : "a"}), "click", function(e){
					if(!e) e = window.event;

					filterGroupsPopup.deselect(BX("filter_field_group_hidden").value.value);
					BX("filter_field_group_hidden").value = "0";
					BX.addClass(BX("filter-field-group").parentNode.parentNode, "webform-field-textbox-empty");
					return BX.PreventDefault(e);
				});
			}

			if (BX("filter-field-user"))
			{
				BX.bind(BX("filter-field-user"), "click", function(e) {
					if(!e) e = window.event;
					filterUserPopup.show();
					return BX.PreventDefault(e);
				});

				BX.bind(BX.findNextSibling(BX("filter-field-user"), {tagName : "a"}), "click", function(e){
					if(!e) e = window.event;
					BX("filter_field_user_hidden").value = "0";
					BX("filter-field-user").value = "";
					BX.addClass(BX("filter-field-user").parentNode.parentNode, "webform-field-textbox-empty");
					return BX.PreventDefault(e);
				});
			}
		});
	}
	else
	{
		filterPopup.show();	
	}

}

function __SLFShowGroupsPopup()
{
	BX('filter-field-user').value = '';
	BX('filter_field_user_hidden').value = "0";

	filterGroupsPopup.show();
}

function __SLFShowUseropup(obj)
{
	filterUserPopup = BX.PopupWindowManager.create("filter-user-popup", obj.parentNode, {
		offsetTop : 1,
		autoHide : true,
		content : BX("FILTER_USER_selector_content"),
		zIndex : 1200,
		buttons : [
			new BX.PopupWindowButton({
				text : BX.message("sonetLDialogClose"),
				className : "popup-window-button-accept",
				events : {
					click : function() {
						this.popupWindow.close();
					}
				}
			})
		]
	});

	if (filterUserPopup.popupContainer.style.display != "block")
	{
		filterUserPopup.show();
	}
}

function __logOnReload(log_counter)
{
	if (BX("sonet_log_counter_preset"))
	{
		if (parseInt(log_counter) > 0)
		{
			BX("sonet_log_counter_preset").style.display = "inline-block";
			BX("sonet_log_counter_preset").innerHTML = log_counter;
		}
		else
		{
			BX("sonet_log_counter_preset").innerHTML = '';
			BX("sonet_log_counter_preset").style.display = "none";
		}
	}
}
