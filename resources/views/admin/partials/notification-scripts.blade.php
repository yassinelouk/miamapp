<script>
$(function ($) {
    "use strict";
// Realtime Order Notification
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;
    
    var pusher = new Pusher('bd457a6ed0c247922b06', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('order-placed-channel');
    channel.bind('order-placed-event', function (data) {
        if ($("#refreshOrder").length > 0) {
            $(".request-loader").addClass("show");
            $("#refreshOrder").load(location.href + " #refreshOrder", function() {
                $(".request-loader").removeClass("show");
            });
        }
        
        audio.play();
        
        // show notification
        var content = {};
        
        content.message = "{{__('New Order Received!')}}";
        content.title = "{{__('Success')}}";
        content.icon = 'fa fa-bell';
        
        $.notify(content, {
            type: 'success',
            placement: {
                from: 'top',
                align: 'right'
            },
            showProgressbar: true,
            time: 1000,
            delay: 2000,
        });
    });


    var waiterCallChannel = pusher.subscribe('waiter-called-channel');
    waiterCallChannel.bind('waiter-called-event', function(data) {
        waiterCallAudio.play();
        
        // show notification
        var content = {};
        
        content.message = '<strong class="text-danger">{{__("Table")}} - ' + data.table + '</strong> {{__("ask for waiter!")}}';
        content.title = "{{__('Need a waiter!')}}";
        content.icon = 'fa fa-bell';
        
        $.notify(content, {
            type: 'secondary',
            placement: {
                from: 'top',
                align: 'right'
            },
            delay: 0,
        });
    });
});
</script>
