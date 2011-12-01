var MessageBox = {
    alert: function(message)
    {
        $(".box_message").remove();
        $('<div>').addClass('box_message').appendTo($('body')).html(message)
        .prepend('<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>')
        .dialog({
                modal: true,
                resizable: false,
                buttons: {
                    Ok: function() {
                        $(this).dialog("close");
                    }
                }
        });
    }
    ,confirm: function (message, action)
    {
        $(".box_message").remove();
        $('<div>').addClass('box_message').appendTo($('body'))
        .html(message)
        .prepend('<span class="ui-icon ui-icon-help" style="float: left; margin-right: .3em;"></span>')
        .dialog({
            modal: true,
            resizable: false,
            buttons: {
                NO: function() {
                    this.what = 'NO';
                    $(this).dialog("close");
                },
                YES: function() {
                    this.what = 'YES';
                    $(this).dialog("close");
                }
            },
            close: function(event, ui){
                if(this.what == 'YES'){
                    action.apply();
                }
            }
        });
    }
    ,dialog: function(content)
    {
        $("#msgbox").remove();
        $('<div>').attr('id', 'msgbox').appendTo($('body'))
        .html(content)
        .dialog({
            autoOpen: false,
            show: "blind",
            hide: "blind",
            resizable: false,
            width: "600",
            maxWidth: "710",
            maxHeight: "340"
        });
    }
};
//MessageBox.confirm('are you sure?', function(){
//  //alert('You are sure do this, I\'v done it!');
//  MessageBox.alert('Everything done');
//});
//MessageBox.alert('Everything done');
