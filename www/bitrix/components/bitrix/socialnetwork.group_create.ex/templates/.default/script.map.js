{"version":3,"file":"script.min.js","sources":["script.js"],"names":["BXOnInviteListChange","window","arInvitationUsersList","arguments","BX","onCustomEvent","util","array_values","BXSwitchExtranet","isChecked","style","display","checked","disabled","addClass","removeClass","selected","BXSwitchNotVisible","BXDeleteImage","visibility","value","tmpNode","findChild","tagName","attr","name","BXGCESwitchTabs","tabs","findChildren","className","blockList","bind","event","target","srcElement","blockOld","blockNew","i","length","posOld","pos","tabsContainer","height","overflow","hasClass","parentNode","id","posNew","fx","time","step","type","start","finish","callback","delegate","this","callback_complete","BXGCESwitchFeatures","servBlock","servList","inputList","toggleClass","BXGCESubmitForm","e","textarea","message","BXGCE","lastAction","actionURL","action","disableSubmitButton","ajax","submitAjax","document","forms","sonet_group_create_popup_form","url","method","dataType","onsuccess","obResponsedata","undefined","showError","SocNetLogDestination","obItems","isArray","selectedUsersOld","selectedUsers","strUserCodeTmp","j","arUserSelector","getAttribute","deleteItem","obItemsSelected","reInit","top","location","href","reload","onfailure","PreventDefault","onCancelClick","__addExtranetEmail","inputMail","emailPattern","test","BXExtranetMailList","background","setTimeout","backgroundColor","link","create","props","children","events","click","__deleteExtranetEmail","appendChild","push","browser","IsIE","focus","item","flag","isDomNode","removeChild","num","parseInt","substring","BXGCEEmailKeyDown","keyCode","userSelector","setSelector","selectorName","disableBackspace","backspaceDisable","unbind","selectCallback","search","bUndeleted","data-id","attrs","html","mouseover","mouseout","BXfpSetLinkName","formName","tagInputName","tagLink1","tagLink2","openDialogCallback","PopupWindow","setOptions","popupZindex","BXfpOpenDialogCallback","apply","bindActionLink","oBlock","PopupMenu","destroy","arItems","text","onclick","onActionSelect","arParams","offsetLeft","offsetTop","zIndex","lightShadow","angle","position","offset","onPopupShow","ob","show","innerHTML","errorText","showMessage","bDisable","oButton","cursor"],"mappings":"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"}