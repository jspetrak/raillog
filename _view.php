<?php

    /**
     *  <p>Simple class which handles all operations necessary to view requested result.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    2nd January 2006
     */
    class Viewer {
        /**
         *  <p>There is no need to make any instance of this class.</p>
         */
        private function __construct() {}
        
        /**
         *  <p>Method which does the vizualization of the required data using appropriate template. In fact
         *  it only requires view file according to the name given by the method parametr from HttpRequest object.</p>
         *
         *  @param $requestedView File name of the template which has to be used to view the requested data.
         */
        public static function vizualize($requestedView) { require( $requestedView ); }
        
        /**
         *  <p>This method parser given date and time and returns date in the format D.M. YYYY (without leading zeros).</p>
         *
         *  @param $datetime Date and time as a UNIX timestamp.
         *  @return string Date in format D.M. YYYY
         */
        public static function viewDate($datetime) { return date('j.n. Y', $datetime); }
        
        /**
         *  <p>This method parser given date and time and returns date and time in the format D.M. YYYY
         *  H:MM (without leading zeros in the date and hour).</p>
         *
         *  @param $datetime Date and time as a UNIX timestamp.
         *  @return string Date and time in format D.M. YYYY H:MM
         */
        public static function viewDateTime($datetime) { return date('j.n. Y G:i', $datetime); }

        /**
         *   @todo SIMPLIFY!!!
         */
        public static function transformSeconds($seconds) {
            if (!is_int($seconds)) $seconds = (int)$seconds;

            $years = 0;
            $weeks = 0;
            $days = 0;
            $hours = 0;
            $minutes = 0;

            $res = '';

            // years
            if ($seconds > 31536000) {
                $years = floor($seconds / 31536000);
                $seconds = $seconds - ($years * 31536000);
            }
            // weeks
            if ($seconds >= 604800) {
                $weeks = floor($seconds / 604800);
                $seconds = $seconds - ($weeks * 604800);
            }
            // days
            if ($seconds >= 86400) {
                $days = floor($seconds / 86400);
                $seconds = $seconds - ($days * 86400);
            }
            // hours
            if ($seconds >= 3600) {
                $hours = floor($seconds / 3600);
                $seconds = $seconds - ($hours * 3600);
            }
            // minutes
            if ($seconds >= 60) {
                $minutes = floor($seconds / 60);
                $seconds = $seconds - ($minutes * 60);
            }

            if ($years > 0) $res .= $years . ' let ';
            if ($weeks > 0) $res .= $weeks . ' týdnů ';
            if ($days > 0) $res .= $days . ' dnů ';
            if ($hours > 0) $res .= $hours . ' hodin ';
            if ($minutes > 0) $res .= $minutes . ' minut ';

            return $res;
        }
    }

?>
