var SystemPage = {
    init :function(){
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true
        });
    },
    dataTableLoaded: function (){
        /** clear any existing binds */
        $('.link-form-edit').unbind('click');
        /** bind the click event here */
        $('.link-form-edit').on('click',function(e){
            /** block the ui */
            App.blockUI({ boxed: true });
            /** request the assets for this link */
            $.post( "/Request/MediaBuyEmail", {
                action: "getEditData",  /** action to run */
                key: jQuery(this).attr("data-link")
            }, function(data) {
                $.unblockUI();
                /** we have our data move the popup to the top of the body */
                $('#email-link-edit').prependTo(document.body);
                /** now put it in a overlay and start populating it */
                $('#email-link-edit').modal();
                var linkData = data['link-data'];
                var promoterData = data['promoter-data'];
                var funnelData = data['funnel-data'];
                /** clear the inputs **/
                SystemPage.clearInputs();
                /** fill in all of the funnels **/
                for(var i = 0; i< funnelData.length; i++ ){
                    $('#funnel').append( $('<option></option>').val(funnelData[i]['key']).html(funnelData[i]['name']) );
                }
                /** fill in all of the promoters **/
                for(var i = 0; i< promoterData.length; i++ ){
                    $('#promoter').append( $('<option></option>').val(promoterData[i]['key']).html(promoterData[i]['name']) );
                }
                /** now update the text boxes **/
                $('#funnel').val(linkData['funnel-key']);
                $('#promoter').val(linkData['promoter-key']);
                $('#link-key').val(linkData['key']);
                $('#name').val(linkData['name']);
                $('#cost').val(linkData['cost']);
                $('#expected-return').val(linkData['expected-return']);
                $('#reportrange').daterangepicker({
                    opens: (App.isRTL() ? 'left' : 'right'),
                    startDate: linkData['start-date'],
                    endDate: linkData['end-date'],
                    minDate: '01-01-2000',
                    maxDate: '12-31-2100',
                    dateLimit: { days: 180 },
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                        'Last 7 Days': [moment().subtract('days', 6), moment()],
                        'Last 30 Days': [moment().subtract('days', 29), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                    },
                    buttonClasses: ['btn'],
                    applyClass: 'green',
                    cancelClass: 'default',
                    format: 'MM-DD-YYYY',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                });
                $('#reportrange span').html(linkData['start-date'] + ' - ' + linkData['end-date']);
                $('#save-button').on('click',SystemPage.saveEditForm)
            },'json');
        });
    }, clearInputs: function(){
        $('#funnel').html(null);
        $('#promoter').html(null);
        $('#funnel').val(null);
        $('#promoter').val(null);
        $('#key').val(null);
        $('#name').val(null);
        $('#cost').val(null);
        $('#expected-return').val(null);
    }, saveEditForm: function(){
        App.blockUI({ boxed: true, target: '#email-link-edit', message: 'Saving' });
        $.post( "/Request/MediaBuyEmail", {
            'action':'saveLink',
            'key': $('#link-key').val(),
            'name': $('#name').val(),
            'promoter-key':$('#promoter').val(),
            'funnel-key':$('#funnel').val(),
            'cost':$('#cost').val(),
            'expected-return':$('#expected-return').val(),
            'start-date':$('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
            'end-date':$('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD')
        }, function(data) {
            App.unblockUI($('#email-link-edit'));
            if(!data.result){
                $.notific8('There was an error processing your request!', { theme: 'ruby', life: 3000 });
            }
            $('#email-link-edit').modal('hide');
            $('.portlet .portlet-title a.reload[data-load="true"]').click();
        },'json');
    }
}

$(document).ready(function() { SystemPage.init();  });
