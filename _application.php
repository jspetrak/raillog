<?php

    require( '_stat.php' );
    require( '_model.php' );
    require( '_logic.php' );
    require( '_view.php' );

    /**
     *  <p>Root class which encapsulates initialization and starting the application.
     *  To start it, just import and call the static method <code>run()</code>.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    2006-12-16		run() improved to not create a local var.
     */
    class Application {
        // private object properties
        private $params;
        private $parser;
        private $caughtException;

        private static $instance;

        /**
         *  <p>This is the contructor. It is hidden because is called by running the class.
         *  It creates all necessary instances of other application components.</p>
         */
        private function Application() {
            $this->params = HttpRequest::get();
            $this->parser = new XmlDataParser($this->params->getDataFileName());
        }

        /**
         *  <p>Private getter method which returns instance of data parser created in the constructor.</p>
         *
         *  @return object IDataParser Parser used to get data from application source to the model.
         */
        private function getParser() { return $this->parser; }

        /**
         *  <p>Private getter method which returns instance of HttpRequest containing request parameters.</p>
         *
         *  @return object HttpRequest Parameters of the request.
         */
        private function getParams() { return $this->params; }

        /**
         *  <p>Returns error occured during the actual session.</p>
         *
         *  @return exception Occured exception
         */
        public function getCaughtException() { return $this->caughtException; }

        public static function getInstance() { return self::$instance; }

        /**
         *  <p>This is the only methods which runs the application. In fact it creates a new
         *  instance of this class because the constructor contains all necessary instructions to set up
         *  application variables and parse required data from XML source. It is as easy as possible 8-D</p>
         */
        public static function run() {
            self::$instance = new Application();

            try {
                self::$instance->getParser()->parse();
                Viewer::vizualize(self::$instance->getParams()->getRequestedView());
            } catch (Exception $e) {
                self::$instance->caughtException = $e;
                Viewer::vizualize(self::$instance->getParams()->getErrorView());
            }
        }
    }

?>
