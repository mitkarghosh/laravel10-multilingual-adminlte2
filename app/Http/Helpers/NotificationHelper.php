<?php
/*****************************************************/
# NotificationHelper
# Page/Class name : NotificationHelper
# Author :
# Created Date : 26-08-2019
# Functionality : 
# Purpose : all general function for push notification
/*****************************************************/
namespace App\Http\Helpers;
use Request;
use \App\PushNotification as PNotification;
use Edujugon\PushNotification\PushNotification;

class NotificationHelper
{
/*****************************************************/
    # NotificationHelper
    # Function name : sendNotification
    # Author        :
    # Created Date  : 23-08-2019
    # Purpose       : send push notification to ios or android
    # Params        : $device,$deviceToken,$message,$title
    /*****************************************************/
    public static function sendNotification($device,$deviceToken,$message,$title)
    {
        
        if($device == 'Android' && $deviceToken){
            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                        'title'=>$title,
                        'body'=>$message,
                        'sound' => 'default'
                        ],
                'data' => [
                        'title'=>$title,
                        'body'=>$message,
                        ]
                ])
                ->setApiKey('AIzaSyAu4pftLwxhWTCRUsrxtX1O_FntxUr5Wg4')
                ->setDevicesToken($deviceToken)
                ->send();
        }else if($device == 'iOS' && $deviceToken){
            $push = new PushNotification('apn');
            $push->setMessage([
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $message
                        ],
                        'sound' => 'default',
                        'badge' => 1
                    ],
                    'extraPayLoad' => [
                        'custom' => 'My custom data',
                    ]
                ])
            ->setDevicesToken($deviceToken)
            ->send();
        }
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationBid
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to bid creator for bid placing
    # Params        : $bidObject
    /*****************************************************/
    public static function sendNotificationBid($bidObject)
    {
        if($bidObject){
            $userName = optional($bidObject->user)->full_name;
            $bidNameEn = '';
            $bidNameAr = '';
            if($bidObject->bid){
                foreach($bidObject->bid->bidLocal as $bidLocalKey => $bidLocalVal){
                    if($bidLocalVal->lang_code == 'EN'){
                        $bidNameEn = $bidLocalVal->bid_title;
                    }else{
                        $bidNameAr = $bidLocalVal->bid_title;
                    }
                }
            }
    
            $insert = array();
            $insert['send_by'] = $bidObject->bid_by;
            $insert['send_to'] = optional($bidObject->bid)->vendor_id;
            $insert['message_en'] = 'Bid amount '.$bidObject->bid_amount.' has been placed by '.$userName.' in '.$bidNameEn;
            $insert['message_ar'] = 'تم وضع مبلغ العطاء '.$bidObject->bid_amount.' بواسطة '.$userName.' في '.$bidNameAr;
            $insert['type'] = 'bid';
            $insert['bid_id'] = $bidObject->bid_id;
            
            PNotification::create($insert);
            $lang = optional(optional($bidObject->bid)->vendor)->loggedin_language;
            if($lang == 'en'){
                $msg = $insert['message_en'];
                $title = "Bid placed";
            }else{
                $msg = $insert['message_ar'];
                $title = "Bid placed";
            }
            self::sendNotification(optional(optional($bidObject->bid)->vendor)->registration_channel,optional(optional($bidObject->bid)->vendor)->device_token,$msg,$title);
        }

    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractCreate
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to contractor for contract creation
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractCreate($contractObject)
    {
        $userName = optional($contractObject->contractCreatorDetails)->full_name;
                
        $insert = array();
        $insert['send_by'] = $contractObject->contract_creator_id;
        $insert['send_to'] = $contractObject->contractor_id;
        $insert['message_en'] = 'Contract "'.$contractObject->title.'" has been created by '.$userName;
        $insert['message_ar'] = 'تم إنشاء عقد "'.$contractObject->title.'" بواسطة '.$userName;
        $insert['type'] = 'contract';
        $insert['contract_id'] = $contractObject->id;
        
        PNotification::create($insert);
        $lang = optional($contractObject->contractor)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract created";
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract created";
        }
        self::sendNotification(optional($contractObject->contractor)->registration_channel,optional($contractObject->contractor)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractDelete
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to contractor for contract deletion
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractDelete($contractObject)
    {
        $userName = optional($contractObject->contractCreatorDetails)->full_name;
                
        $insert = array();
        $insert['send_by'] = $contractObject->contract_creator_id;
        $insert['send_to'] = $contractObject->contractor_id;
        $insert['message_en'] = 'Contract "'.$contractObject->title.'" has been deleted by '.$userName;
        $insert['message_ar'] = 'تم حذف عقد "'.$contractObject->title.'" بواسطة '.$userName;
        $insert['type'] = 'contract';
        $insert['contract_id'] = $contractObject->id;
        PNotification::create($insert);
        $lang = optional($contractObject->contractor)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract deleted";
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract deleted";
        }
        self::sendNotification(optional($contractObject->contractor)->registration_channel,optional($contractObject->contractor)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationOrder
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to wholesaler for order placement
    # Params        : $orderObject
    /*****************************************************/
    public static function sendNotificationOrder($orderObject)
    {
        if($orderObject->orderDetails->count()){
            foreach($orderObject->orderDetails as $oDetailKey => $oDetailVal){
                if($oDetailVal->orderDetailLocals->count()){
                    foreach($oDetailVal->orderDetailLocals as $oDetailLocalKey => $oDetailLocalVal){
                        if($oDetailLocalVal->lang_code == 'EN'){
                            $productNameEn = $oDetailLocalVal->local_product_name;
                        }else{
                            $productNameAr = $oDetailLocalVal->local_product_name;
                        }
                    }
                }else{
                    $productNameEn = '';
                    $productNameAr = '';
                }
                $insert = array();
                $insert['send_by'] = $orderObject->user_id;
                $insert['send_to'] = $oDetailVal->vendor_id;
                $insert['message_en'] = 'Order has been placed for '.$productNameEn.' by '.optional($orderObject->userDetails)->full_name;
                $insert['message_ar'] = 'تم تقديم الطلب لـ '.$productNameAr.' بواسطة '.optional($orderObject->userDetails)->full_name;
                $insert['type'] = 'order';
                $insert['order_id'] = $oDetailVal->order_id;
                $insert['order_details_id'] = $oDetailVal->id;
                
                PNotification::create($insert);
                $lang = optional($oDetailVal->vendor)->loggedin_language;
                if($lang == 'en'){
                    $msg = $insert['message_en'];
                    $title = "Order placed";
                }else{
                    $msg = $insert['message_ar'];
                    $title = "Order placed";
                }
                self::sendNotification(optional($oDetailVal->vendor)->registration_channel,optional($oDetailVal->vendor)->device_token,$msg,$title);
            }
            
        }
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationOrderCancel
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to wholesaler for order cancellation
    # Params        : $orderObject
    /*****************************************************/
    public static function sendNotificationOrderCancel($orderObject)
    {
        $productNameEn = '';
        $productNameAr = '';
        $userName = optional(optional($orderObject->order)->userDetails)->full_name;
        foreach($orderObject->orderDetailLocals as $oDetailLocalKey => $oDetailLocalVal){
            if($oDetailLocalVal->lang_code == 'EN'){
                $productNameEn = $oDetailLocalVal->local_product_name;
            }else{
                $productNameAr = $oDetailLocalVal->local_product_name;
            }
        }
        $insert = array();
        $insert['send_by'] = optional($orderObject->order)->user_id;
        $insert['send_to'] = $orderObject->vendor_id;
        $insert['message_en'] = 'Order has been canceled for '.$productNameEn.' by '.$userName;
        $insert['message_ar'] = 'تم إلغاء الطلب لـ '.$productNameAr.' بواسطة '.$userName;
        $insert['type'] = 'order';
        $insert['order_id'] = $orderObject->order_id;
        $insert['order_details_id'] = $orderObject->id;

        PNotification::create($insert);
        $lang = optional($orderObject->vendor)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Order canceled";
        }else{
            $msg = $insert['message_ar'];
            $title = "Order canceled";
        }
        self::sendNotification(optional($orderObject->vendor)->registration_channel,optional($orderObject->vendor)->device_token,$msg,$title);
        
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationOrderStatus
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to customer for order status change
    # Params        : $orderObject
    /*****************************************************/
    public static function sendNotificationOrderStatus($orderObject)
    {
        $productNameEn = '';
        $productNameAr = '';
        $statusEn = array('O' => 'ordered', 'P' => 'packed', 'S' => 'shipped', 'D' => 'delivered');
        $statusAr = array('O' => 'تم الطلب', 'P' => 'تم التغليف', 'S' => 'تم الشحن', 'D' => 'تم التوصيل');
        $userName = optional(optional($orderObject->order)->userDetails)->full_name;
        foreach($orderObject->orderDetailLocals as $oDetailLocalKey => $oDetailLocalVal){
            if($oDetailLocalVal->lang_code == 'EN'){
                $productNameEn = $oDetailLocalVal->local_product_name;
            }else{
                $productNameAr = $oDetailLocalVal->local_product_name;
            }
        }
        
        $insert = array();
        $insert['send_by'] = $orderObject->vendor_id;
        $insert['send_to'] = optional($orderObject->order)->user_id;
        $insert['message_en'] = $productNameEn.' product has been '.$statusEn[$orderObject->order_status].' of the order with id '.optional($orderObject->order)->unique_order_id;
        $insert['message_ar'] = 'لقد كان منتج '.$productNameAr.' هو '.$statusAr[$orderObject->order_status].' للطلب بمعرف '.optional($orderObject->order)->unique_order_id;
        $insert['type'] = 'order';
        $insert['order_id'] = $orderObject->order_id;
        $insert['order_details_id'] = $orderObject->id;

        PNotification::create($insert);
        $lang = optional(optional($orderObject->order)->userDetails)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Order updated";
        }else{
            $msg = $insert['message_ar'];
            $title = "Order updated";
        }
        self::sendNotification(optional(optional($orderObject->order)->userDetails)->registration_channel,optional(optional($orderObject->order)->userDetails)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractAction
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to customer for contract accept/reject
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractAction($contractObject)
    {
        $userName = optional($contractObject->userContractorDetails)->full_name;
        $statusEn = array('A' => 'accepted', 'R' => 'rejected');
        $statusAr = array('A' => 'تم القبول', 'R' => 'تم الرفض');
        $insert = array();
        $insert['send_by'] = $contractObject->contractor_id;
        $insert['send_to'] = $contractObject->contract_creator_id;
        $insert['message_en'] = 'Contract "'.$contractObject->title.'" has been '.$statusEn[$contractObject->status].' by '.$userName;
        $insert['message_ar'] = 'العقد: كان "'.$contractObject->title.'" '.$statusAr[$contractObject->status].' بواسطة '.$userName;
        $insert['type'] = 'contract';
        $insert['contract_id'] = $contractObject->id;
        
        PNotification::create($insert);
        $lang = optional($contractObject->contractCreatorDetails)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract ".$statusEn[$contractObject->status];
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract ".$statusAr[$contractObject->status];
        }
        self::sendNotification(optional($contractObject->contractCreatorDetails)->registration_channel,optional($contractObject->contractCreatorDetails)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractStatusChange
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to contractor and customer for contract status change
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractStatusChange($contractObject)
    {
        $statusEn = array('O' => 'started', 'C' => 'completed', 'E' => 'expired');
        $statusAr = array('O' => 'بدأت', 'C' => 'اكتمل', 'E' => 'انتهت الصلاحية');
        $insert = array();
        $insert['send_by'] = $contractObject->contractor_id;
        $insert['send_to'] = $contractObject->contract_creator_id;
        $insert['message_en'] = 'Contract "'.$contractObject->title.'" has been '.$statusEn[$contractObject->status];
        $insert['message_ar'] = 'العقد: كان "'.$contractObject->title.'" '.$statusAr[$contractObject->status];
        $insert['type'] = 'contract';
        $insert['contract_id'] = $contractObject->id;
        
        PNotification::create($insert);
        $lang = optional($contractObject->contractCreatorDetails)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract ".$statusEn[$contractObject->status];
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract ".$statusAr[$contractObject->status];
        }
        self::sendNotification(optional($contractObject->contractCreatorDetails)->registration_channel,optional($contractObject->contractCreatorDetails)->device_token,$msg,$title);

        $insert = array();
        $insert['send_by'] = $contractObject->contract_creator_id;
        $insert['send_to'] = $contractObject->contractor_id;
        $insert['message_en'] = 'Contract "'.$contractObject->title.'" has been '.$statusEn[$contractObject->status];
        $insert['message_ar'] = 'العقد: كان "'.$contractObject->title.'" '.$statusAr[$contractObject->status];
        $insert['type'] = 'contract';
        $insert['contract_id'] = $contractObject->id;
        
        PNotification::create($insert);
        $lang = optional($contractObject->contractor)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract ".$statusEn[$contractObject->status];
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract ".$statusAr[$contractObject->status];
        }
        self::sendNotification(optional($contractObject->contractor)->registration_channel,optional($contractObject->contractor)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractShipmentNote
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to customer for contract shipment note
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractShipmentNote($contractObject)
    {
        $insert = array();
        $insert['send_by'] = optional($contractObject->contract)->contractor_id;
        $insert['send_to'] = optional($contractObject->contract)->contract_creator_id;
        $insert['message_en'] = 'Contract shipment has been updated for the contract "'.optional($contractObject->contract)->title.'"';
        $insert['message_ar'] = 'تم تحديث شحن العقد للعقد "'.optional($contractObject->contract)->title.'"';
        $insert['type'] = 'contract';
        $insert['contract_id'] = optional($contractObject->contract)->id;
        
        PNotification::create($insert);
        $lang = optional(optional($contractObject->contract)->contractCreatorDetails)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Contract shipment added";
        }else{
            $msg = $insert['message_ar'];
            $title = "Contract shipment added";
        }
        self::sendNotification(optional(optional($contractObject->contract)->contractCreatorDetails)->registration_channel,optional(optional($contractObject->contract)->contractCreatorDetails)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationContractShipmentNoteAccepted
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to contractor for contract shipment note accepted by customer
    # Params        : $contractObject
    /*****************************************************/
    public static function sendNotificationContractShipmentNoteAccepted($contractObject)
    {
        $insert = array();
        $insert['send_by'] = optional($contractObject->contract)->contract_creator_id;
        $insert['send_to'] = optional($contractObject->contract)->contractor_id;
        $insert['message_en'] = 'Product has been received successfully for the contract "'.optional($contractObject->contract)->title.'"';
        $insert['message_ar'] = 'تم استلام المنتج بنجاح للعقد "'.optional($contractObject->contract)->title.'"';
        $insert['type'] = 'contract';
        $insert['contract_id'] = optional($contractObject->contract)->id;
        
        PNotification::create($insert);
        $lang = optional(optional($contractObject->contract)->contractor)->loggedin_language;
        if($lang == 'en'){
            $msg = $insert['message_en'];
            $title = "Product received successfully";
        }else{
            $msg = $insert['message_ar'];
            $title = "Product received successfully";
        }
        self::sendNotification(optional(optional($contractObject->contract)->contractor)->registration_channel,optional(optional($contractObject->contract)->contractor)->device_token,$msg,$title);
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationBidWinner
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to customer for bid winner
    # Params        : $bidObject
    /*****************************************************/
    public static function sendNotificationBidWinner($bidObject)
    {
        if($bidObject){
            // $userName = optional($bidObject->user)->full_name;
            $bidNameEn = '';
            $bidNameAr = '';
            if($bidObject){
                foreach($bidObject->bidLocal as $bidLocalKey => $bidLocalVal){
                    if($bidLocalVal->lang_code == 'EN'){
                        $bidNameEn = $bidLocalVal->bid_title;
                    }else{
                        $bidNameAr = $bidLocalVal->bid_title;
                    }
                }
            }
    
            $insert = array();
            $insert['send_by'] = $bidObject->vendor_id;
            $insert['send_to'] = $bidObject->winner_id;
            $insert['message_en'] = 'Congratulation, you have been selected winner for the bid "'.$bidNameEn.'", please confirm/accept within 48hrs.';
            $insert['message_ar'] = 'تهانينا ، لقد تم اختيارك للفائز بالمزايدة "'.$bidNameAr. '" ، يرجى تأكيد / قبول خلال 48 ساعة';
            $insert['type'] = 'bid';
            $insert['bid_id'] = $bidObject->id;
            
            PNotification::create($insert);
            $lang = optional($bidObject->bidWinnerDetails)->loggedin_language;
            if($lang == 'en'){
                $msg = $insert['message_en'];
                $title = "Bid winner request";
            }else{
                $msg = $insert['message_ar'];
                $title = "Bid winner request";
            }
            self::sendNotification(optional($bidObject->bidWinnerDetails)->registration_channel,optional($bidObject->bidWinnerDetails)->device_token,$msg,$title);
        }
    }

    /*****************************************************/
    # NotificationHelper
    # Function name : sendNotificationBidWinnerAccept
    # Author        :
    # Created Date  : 26-08-2019
    # Purpose       : send push notification to wholesaler and customer for bid winner acceptance
    # Params        : $bidObject
    /*****************************************************/
    public static function sendNotificationBidWinnerAccept($bidObject)
    {
        if($bidObject){
            $userName = optional($bidObject->bidWinnerDetails)->full_name;
            $bidNameEn = '';
            $bidNameAr = '';
            if($bidObject){
                foreach($bidObject->bidLocal as $bidLocalKey => $bidLocalVal){
                    if($bidLocalVal->lang_code == 'EN'){
                        $bidNameEn = $bidLocalVal->bid_title;
                    }else{
                        $bidNameAr = $bidLocalVal->bid_title;
                    }
                }
            }
    
            $insert = array();
            $insert['send_by'] = $bidObject->winner_id;
            $insert['send_to'] = $bidObject->vendor_id;
            $insert['message_en'] = $userName.' has been awarded for the bid "'.$bidNameEn.'"';
            $insert['message_ar'] = 'تم منح '.$userName.' للعطاء "'.$bidNameAr.'"';
            $insert['type'] = 'bid';
            $insert['bid_id'] = $bidObject->id;
            
            PNotification::create($insert);
            $lang = optional($bidObject->vendor)->loggedin_language;
            if($lang == 'en'){
                $msg = $insert['message_en'];
                $title = "Bid winner";
            }else{
                $msg = $insert['message_ar'];
                $title = "Bid winner";
            }
            self::sendNotification(optional($bidObject->vendor)->registration_channel,optional($bidObject->vendor)->device_token,$msg,$title);

            $insert = array();
            $insert['send_by'] = $bidObject->vendor_id;
            $insert['send_to'] = $bidObject->winner_id;
            $insert['message_en'] = 'Congratulations, "'.$bidNameEn.'" have been awarded to you';
            $insert['message_ar'] = 'تهانينا ، "'.$bidNameAr.'" تم منحها لك';
            $insert['type'] = 'bid';
            $insert['bid_id'] = $bidObject->id;
            
            PNotification::create($insert);
            $lang = optional($bidObject->bidWinnerDetails)->loggedin_language;
            if($lang == 'en'){
                $msg = $insert['message_en'];
                $title = "Bid winner";
            }else{
                $msg = $insert['message_ar'];
                $title = "Bid winner";
            }
            self::sendNotification(optional($bidObject->bidWinnerDetails)->registration_channel,optional($bidObject->bidWinnerDetails)->device_token,$msg,$title);
        }
    }
}