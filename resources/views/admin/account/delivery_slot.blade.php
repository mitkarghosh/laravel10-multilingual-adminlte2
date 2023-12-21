@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $data['page_title'] }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                
                @include('admin.elements.notification')

                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.delivery-slots'],
                                    'name'  => 'updateSiteSettingsForm',
                                    'id'    => 'updateSiteSettingsForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                    @php $k = 1; @endphp
                    @foreach ($deliverySlots as $key => $slot)
                        @php if (\App::getLocale() == 'en') $name = $slot->day_title; else $name = $slot->day_title_de; @endphp
                        <input type="hidden" name='delivery[id][]' readonly class="form-control" value="{{$slot->id}}">
                        <div class="main-row-for-time row addField_{{$key}}" data-id="{{$key}}">                        
                            <div class="">
                                <div class="col-md-3">
                                    <div class="form-group">
                                    @if ($key == 0)
                                        <label for="FirstName">@lang('custom_admin.lab_delivery_days')<span class="red_star">*</span></label>
                                    @endif
                                        {{ Form::text('delivery[day_title][]', $name, array(
                                                                                            'class' => 'form-control',
                                                                                            'placeholder' => '',
                                                                                            'required' => 'required',
                                                                                            'readonly'  => true )) }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                    @if ($key == 0)
                                        <label for="LastName">@lang('custom_admin.lab_delivery_holiday')<span class="red_star">*</span></label>
                                    @endif
                                        <div><input type="checkbox" name='delivery[holiday][{{$key}}]' value="1" @if ($slot->holiday == '1') checked @endif></div>
                                    </div>
                                </div>
                                @php
                                 $daydata=Helper::getSloatByDayId($slot->id);
                                $startTime=date('H:i');
                                $endTime=date('H:i');
                                @endphp
                                @if($daydata)
                                @php $sloatindex=0; @endphp
                                @foreach($daydata as $daysData) 
                                   @php $startTime= date('H:i', strtotime($daysData->start_time)); 
                                    $endTime=date('H:i', strtotime($daysData->end_time)); @endphp
                                    @if($sloatindex==0)
                                        <div class="col-md-3">
                                            <div class="form-group">                                    
                                                <label for="LastName">@lang('custom_admin.lab_delivery_start_time')<span class="red_star">*</span></label>
                                                <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                    <input type="text" name='delivery[slot][{{$key}}][start_time][]' readonly class="form-control StartTimeSlot" value="{{$startTime}}">
                                                </div>                                    
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">                                  
                                                <label for="LastName">@lang('custom_admin.lab_delivery_end_time')<span class="red_star">*</span></label>
                                                <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                    <input type="text" name='delivery[slot][{{$key}}][end_time][]' readonly class="form-control EndTimeSlot" value="{{$endTime}}">
                                                </div>
                                            </div>
                                        </div>                          
                                        <div class="col-md-1 addMoreRows_{{$key}}">                               
                                            <label for="title">&nbsp;</label><br />                                
                                            <button class="btn btn-success add-more" data-attrkey="{{$key}}" type="button"><i class="fa fa-plus"></i></button>
                                        </div>  
                                        @php $sloatindex++; @endphp
                                      @endif  
                                 @endforeach 
                               @endif
                            </div> 

                            @if($daydata)
                                @php $sloatindex=0; @endphp
                                @foreach($daydata as $daysData) 
                                   @php $startTime= date('H:i', strtotime($daysData->start_time)); 
                                    $endTime=date('H:i', strtotime($daysData->end_time)); @endphp
                                  @if($sloatindex>0)
                                            <div class="new_row_0 new-sloat-row" style="margin-top: 10px;">
                                            <div class="col-md-3">
                                                <div class="form-group"><label for="FirstName">&nbsp;</label></div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="LastName">&nbsp;</label>
                                                    <div></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                            <div class="form-group">  
                                                <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                    <input type="text" name='delivery[slot][{{$key}}][start_time][]' readonly class="form-control StartTimeSlot" value="{{$startTime}}">
                                                </div>                                    
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">  
                                                <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                    <input type="text" name='delivery[slot][{{$key}}][end_time][]' readonly class="form-control EndTimeSlot" value="{{$endTime}}">
                                                </div>
                                            </div>
                                        </div>                          
                                        <div class="col-md-1 addMoreRows_{{$key}}">    
                                             <a class="deleteRow btn btn-danger ibtnDel" data-attrkey="6" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                       </div>  
                                     </div> 
                                        @endif 
                                        @php $sloatindex++; @endphp
                                 @endforeach 
                               @endif 
                        </div> 
                        @if(count($deliverySlots)!=$k)
                        <hr>
                        @endif
                        @php $k++; @endphp
                       
                    @endforeach
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary update-slot-btn">@lang('custom_admin.btn_update')</button>
                                <a href="{{ route('admin.'.\App::getLocale().'.dashboard') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script type="text/javascript">$(function() {
	var currentTime = '';

	// Add more section start //
	$(document).on('click', '.add-more', function() {
		var keyAttr = $(this).data('attrkey');
		var cols = '';
		var newRow = $('<div class="new_row_' + keyAttr + ' new-sloat-row" style="margin-top: 10px;">');
		cols += '<div class="col-md-3"><div class="form-group"><label for="FirstName">&nbsp;</label></div></div>';
		cols += '<div class="col-md-2"><div class="form-group"><label for="LastName">&nbsp;</label><div></div></div></div>';
		cols += '<div class="col-md-3"><div class="form-group"><div class="input-group clockpicker " data-placement="left" data-align="top" data-autoclose="true"><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span><input type="text" name="delivery[slot][' + keyAttr + '][start_time][]"  readonly class="StartTimeSlot form-control" value="' + currentTime + '"></div></div></div>';
		cols += '<div class="col-md-3"><div class="form-group"><div class="input-group clockpicker " data-placement="left" data-align="top" data-autoclose="true"><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span><input type="text" name="delivery[slot][' + keyAttr + '][end_time][]"  readonly class="EndTimeSlot form-control" value="' + currentTime + '"></div></div></div>';
		cols += '<div class="col-sm-1"><a class="deleteRow btn btn-danger ibtnDel" data-attrkey="' + keyAttr + '" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
		cols += '</div>';
		newRow.append(cols);
		$(".addField_" + keyAttr).append(newRow);
		$('.clockpicker').clockpicker({
			placement: 'bottom',
			align: 'left',
			donetext: 'Done'
		});
		SetTimeIndexing();
		// $(this).remove();
	});
	$(".row").on("click", ".ibtnDel", function(event) {
		thisdiv=$(this)
		$(this).closest('.new-sloat-row').remove();
		//$(document).find('.custom_time_error').remove();
		
		SetTimeIndexing();
		
		setTimeout(function() {
			StartTimeCheckToLastTime();
		}, 100);
		// var keyAttr = $(this).data('attrkey');
		// $('.new_row_'+keyAttr).remove();
		// $('.addMoreRows_'+keyAttr).append('<button class="btn btn-success add-more" id="addrow" data-attrkey="'+keyAttr+'" type="button"><i class="fa fa-plus"></i></button>');
	});
	// Add more section end //
});

function SetTimeIndexing() {
	var i = 0;
	$(document).find('.StartTimeSlot').each(function() {
		$(this).attr('data-id', i);
		i++;
	})
	var i = 0;
	$(document).find('.EndTimeSlot').each(function() {
		$(this).attr('data-id', i);
		i++;
	})

	$(document).find('.main-row-for-time').each(function(){
					var ki = 0;
					$(this).find('.StartTimeSlot').each(function() {
						//$(this).removeAttr('data-rank');
						$(this).attr('data-rank', ki);
						ki++;
					})
					var kii = 0;
					$(this).find('.EndTimeSlot').each(function() {
						//$(this).removeAttr('data-rank');
						$(this).attr('data-rank', kii);
						kii++;
					})

	})
}

$(document).on('click', '.update-slot-btn', function() {
	var errorlength = $(document).find('.custom_time_error').length;
	//StartTimeCheckToLastTime();
    checkallEqualTime();
	var emptyCheck = [];

	$(document).find('.StartTimeSlot').each(function() {
		if ($(this).val() == '') {
			emptyCheck.push(1);
			var thisdiv = $(this);
			thisdiv.closest('.input-group').find('.custom_time_error').remove();
			thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
		}
	})
	$(document).find('.EndTimeSlot').each(function() {
		if ($(this).val() == '') {
			emptyCheck.push(1);
			var thisdiv = $(this);
			thisdiv.closest('.input-group').find('.custom_time_error').remove();
			thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
		}
	})
	//alert();
	if (emptyCheck.length || $(document).find('.custom_time_error').length) {

		$('html,body').animate({
				scrollTop: $(".custom_time_error").offset().top - 60
			},
			'slow');

		return false;

	}
	

})

SetTimeIndexing();

function StartTimeCheckToLastTime() {

	$(document).find('.StartTimeSlot').each(function() {
		var ctrl = $(this).closest('.main-row-for-time');
		var start_time = tConvert($(this).val());
		//$(this).removeClass('form-invalid');
		var thisdiv = $(this);
		$(document).find('.errot_time').remove();
		if (start_time != null && start_time != "" && start_time != undefined) {
			//var ctrls = ctrl.split('_');

			var day = ctrl.data('id');

			var rank = thisdiv.attr('data-rank');
           // thisdiv.attr('data-vs',rank)
          
			
			var validTime = start_time.match(/^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM]$/);

			if (!validTime) {
				// $(this).addClass('form-invalid');
				// $("#errorMsg").html('Enter valid time for ' + day + '.');
				// $('html, body').animate({ scrollTop: 0 }, 'slow');
				// $("#"+ctrl).addClass('form-invalid');
				// return false;
			}

			//alert(rank);

			if (parseInt(rank) > 0) {
				$inds = Number(rank - 1);
				var end_time = ctrl.find(".EndTimeSlot[data-rank='" + $inds + "']").val();
				var start_prev_time = ctrl.find(".StartTimeSlot[data-rank='" + $inds + "']").val();
				/**
				 * 24 hour time check
				 */
				var stt1 = new Date("January 24, 1984 " + start_prev_time);
				stt1 = stt1.getTime();
				var endt1 = new Date("January 24, 1984 " + end_time);
				endt1 = endt1.getTime();


				if (stt1 > endt1) {
					// console.log(rank);
					//  console.log(start_prev_time);
					//  console.log(end_time);
					thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
					isStartTimeError = true;
					return false;
				}
				/**
				 * 24 hour time check end
				 */


				if (end_time != null && end_time != "" && end_time != undefined) {
					//convert both time into timestamp
					var stt = new Date("January 24, 1984 " + start_time);
					stt = stt.getTime();

					var endt = new Date("January 24, 1984 " + end_time);
					endt = endt.getTime();
// console.log(stt);
// console.log(endt);
					if (stt <= endt) {
						//alert('Start time must be bigger than last end time.');
						// thisdiv.val('');
						thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
						isStartTimeError = true;
						return false;
					} else {
						$("#StartErrorMsg").html('');
						isStartTimeError = false;
					}
				}
			}
		}
	});
}

/**@Cehck Time */
$(document).on('blur change', '.StartTimeSlot,.EndTimeSlot', function() {
	$(document).find('.errot_time').remove();
	var thisdiv = $(this);
	var ids = $(this).data('id');
	// $(document).find('.update-slot-btn').prop('disabled',true);
	var strStartTime = $(document).find('.StartTimeSlot[data-id="' + ids + '"]').val();
	var strEndTime = $(document).find('.EndTimeSlot[data-id="' + ids + '"]').val();
	if (strStartTime && strEndTime) {
		if (strStartTime == strEndTime) {
			thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
			return false;
		}
		// var amcheck = tConvert(strStartTime).search("AM");
		// var amcheck1 = tConvert(strEndTime).search("AM");
		// // console.log(amcheck);
		// // console.log(amcheck1);
		// if (amcheck >= 0 && amcheck1 >= 0) {
		// 	// alert(Compare(tConvert(strStartTime),tConvert(strEndTime)));
		// 	if (Compare(tConvert(strStartTime), tConvert(strEndTime)) == 1) {
		// 		thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
		// 		return false;
		// 	}
		// }
		// var pmcheck = tConvert(strStartTime).search("PM");
		// var pmcheck1 = tConvert(strEndTime).search("PM");
		// if (pmcheck >= 0 && pmcheck1 >= 0) {
		// 	if (Compare(tConvert(strStartTime), tConvert(strEndTime)) == 1) {
		// 		thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
		// 		return false;
		// 	}
		// }

	}
	StartTimeCheckToLastTime();
})

function Compare(strStartTime, strEndTime) {
	// var strStartTime = document.getElementById("txtStartTime").value;
	// var strEndTime = document.getElementById("txtEndTime").value;

	var startTime = new Date().setHours(GetHours(strStartTime), GetMinutes(strStartTime), 0);
	var endTime = new Date(startTime)
	endTime = endTime.setHours(GetHours(strEndTime), GetMinutes(strEndTime), 0);
	if (startTime > endTime) {
		//alert("Start Time is greater than end time");
		return 1;
	}
	return 0;
	// if (startTime == endTime) {
	//     alert("Start Time equals end time");
	// }
	// if (startTime < endTime) {
	//     alert("Start Time is less than end time");
	// }
}

function GetHours(d) {
	var h = parseInt(d.split(':')[0]);
	if (d.split(':')[1].split(' ')[1] == "PM") {
		h = h + 12;
	}
	return h;
}

function GetMinutes(d) {
	return parseInt(d.split(':')[1].split(' ')[0]);
}

function tConvert(time) {
	if (time) {
		// Check correct time format and split into components
		time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
		if (time.length > 1) { // If time format correct
			time = time.slice(1); // Remove full string match value
			time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
			time[0] = +time[0] % 12 || 12; // Adjust hours
		}
		return time.join(''); // return adjusted time or original string
	}
}

StartTimeCheckToLastTime();


function checkallEqualTime(){
    $(document).find('.StartTimeSlot').each(function(){
            ids=$(this).data('id');
            thisdiv=$(this);
            var strStartTime = $(document).find('.StartTimeSlot[data-id="' + ids + '"]').val();
            var strEndTime = $(document).find('.EndTimeSlot[data-id="' + ids + '"]').val();
            if (strStartTime && strEndTime) {
                if (strStartTime == strEndTime) {
                    thisdiv.closest('.input-group').after('<label class="error errot_time custom_time_error">@lang("custom_admin.lab_valid_time")</label>');
                    //return false;
                }
            }
    })
}
</script>

@endsection