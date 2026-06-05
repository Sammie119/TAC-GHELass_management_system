<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    namespace App\Zenoph\Notify\Build\Writer;

    use App\Zenoph\Notify\Compose\Composer;
    use App\Zenoph\Notify\Compose\SMSComposer;
    use App\Zenoph\Notify\Compose\VoiceComposer;

    interface IDataWriter {
        function &writeScheduledMessageUpdateRequest(Composer $mc);
        function &writeScheduledMessagesLoadRequest($filter);
        function &writeDestinationsData(Composer $mc);
        function &writeSMSRequest(SMSComposer $sc);
        function &writeVoiceRequest(VoiceComposer $vc);
        function &writeUSSDRequest($ucArr);
        function &writeDestinationsDeliveryRequest(array $messageIdsArr);
    }
