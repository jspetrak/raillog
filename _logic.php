<?php

    /**
     *  <p>Interface defining common API of any data parser used by this application.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    31st October 2005
     */
    interface IDataParser {
        /**
         *  <p>Methods which parses all available data from the source and stores them in the model.</p>
         */
        public function parse();
    }

    /**
     *  <p>Data parser which reads the data from any XML file (in the RailLog format). The path to
     *  the data file has to be given by the parameter of the constructor.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     */
    class XmlDataParser implements IDataParser {
        private $file;

        /**
         *  <p>Constructor. Creates an instance of the XML data parser and set ups the path to the data file.</p>
         *
         *  @param $file Path to the datafile.
         */
        public function __construct($file) {
            $this->file = $file;
        }

        /**
            <p>Methods which parses all available data from the XML data file and stores them in the model.</p>

            @return void
         */
        public function parse() {
            $model =& ApplicationModel::getInstance();
            $data = simplexml_load_file($this->file);

            // parsing the traveller
            if (isset($data->traveller) && count($data->traveller) == 1) {
                $travellerNode = $data->traveller[0];
                $traveller = new Traveller((string)$travellerNode->id, (string)$travellerNode->name);

                foreach ($travellerNode->aim as $aim) { $traveller->setAim((string)$aim); }
                foreach ($travellerNode->icq as $icq) { $traveller->setIcq((string)$icq); }
                foreach ($travellerNode->msn as $msn) { $traveller->setMsn((string)$msn); }

                if (isset($travellerNode->nick)) $traveller->setNick((string)$travellerNode->nick);

                foreach ($travellerNode->email as $email) { $traveller->setEmail((string)$email); }
                foreach ($travellerNode->jabber as $jabber) { $traveller->setJabber((string)$jabber); }
                foreach ($travellerNode->weblog as $weblog) { $traveller->setWeblog((string)$weblog); }
                foreach ($travellerNode->homepage as $homepage) { $traveller->setHomepage((string)$homepage); }

                $model->setTraveller($traveller);
            }

						// parsing the tickets list
						if (isset($data->tickets) && count($data->tickets) == 1) {
							foreach ($data->tickets[0]->ticket as $ticket) {
                				$t = new Ticket((string)$ticket['id'], (string)$ticket['name']);
                				if (isset($ticket['currency'])) $t->setCurrency((string)$ticket['currency']);
                				if (isset($ticket['price'])) $t->setPrice((string)$ticket['price']);
								$t->setArchived((isset($ticket['archived']) && (string)$ticket['archived'] == 'true') ? true : false);
								$model->addTicket($t);
							}
						}

            // parsing the station list
            if (isset($data->stations) && count($data->stations) == 1) {
                foreach ($data->stations[0]->station as $station) {
                    $model->addStation(new Station((string)$station['id'], (string)$station));
                }
            }

            // parsing the GVDs
            if (isset($data->gvds) && count($data->gvds) == 1) {
                foreach ($data->gvds[0]->gvd as $gvdNode) {
                    $gvd = new Gvd((string)$gvdNode['id']);
                    $gvd->setStart((string)$gvdNode['start']);
                    $gvd->setEnd((string)$gvdNode['end']);

                    if (isset($gvdNode->trains) && count($gvdNode->trains) == 1) {
                        foreach ($gvdNode->trains[0]->train as $trainNode) {
													$t = new Train((string)$trainNode['id'], (string)$trainNode);
                          if (isset($trainNode['noteRoad'])) {
                          	$t->setNoteRoad((string)$trainNode['noteRoad']);
                          	if (array_key_exists('orderName', $trainNode)) {
                          	  $t->setOrderName((string)$trainNode['orderName']);
                          	}
                          }
                          $gvd->addTrain($t);
                        }
                    }

                    if (isset($gvdNode->journeys) && count($gvdNode->journeys) == 1) {
                        foreach ($gvdNode->journeys[0]->journey as $journeyNode) {
                            $journey = new Journey(
                                (isset($journeyNode['planned']) && (string)$journeyNode['planned'] == 'true') ? true : false
                            );

                            foreach ($journeyNode->journeypart as $journeyPartNode) {
                                $journeyPart = new JourneyPart();
																$journeyPart->setLength((int)$journeyPartNode['length']);

																$isDefinedCarOrTrainFrom = isset($journeyPartNode['carOrTrainFrom']);
																$isDefinedCarOrTrainTo = isset($journeyPartNode['carOrTrainTo']);
																$isDefinedRenamedFrom = isset($journeyPartNode['renamedFrom']);
																$isDefinedRenamedTo = isset($journeyPartNode['renamedTo']);

																$journeyPart->setStart((string)$journeyPartNode['start']);
																$journeyPart->setEnd((string)$journeyPartNode['end']);

                                $_trainId = (string)$journeyPartNode['train'];
                                $_train = $gvd->getTrainById($_trainId);
                                if (is_null($_train)) throw new ObjectNotFoundException($_trainId);
                                $journeyPart->setTrain($_train);

                                $_stationId = (string)$journeyPartNode['startStation'];
                                $_station = $model->getStationById($_stationId);
                                if (is_null($_station)) throw new ObjectNotFoundException($_stationId);
                                $journeyPart->setStartStation($_station);

                                $_stationId = (string)$journeyPartNode['endStation'];
                                $_station = $model->getStationById($_stationId);
                                if (is_null($_station)) throw new ObjectNotFoundException($_stationId);
                                $journeyPart->setEndStation($_station);

																if ($isDefinedCarOrTrainFrom) {
																	$_trainFromId = (string)$journeyPartNode['carOrTrainFrom'];
																	if (empty($_trainFromId) == false) {
																		$_trainFrom = $gvd->getTrainById($_trainFromId);
																		if (is_null($_trainFrom))
																			throw new ObjectNotFoundException($_trainFromId);
																		$journeyPart->setCarOrTrainFrom($_trainFrom);
																	}
																}

																if ($isDefinedCarOrTrainTo) {
																	$_trainToId = (string)$journeyPartNode['carOrTrainTo'];
																	if (empty($_trainToId) == false) {
																		$_trainTo = $gvd->getTrainById($_trainToId);
																		if (is_null($_trainTo))
																			throw new ObjectNotFoundException($_trainToId);
																		$journeyPart->setCarOrTrainTo($_trainTo);
																	}
																}

																if ($isDefinedRenamedFrom) {
																	$_trainRenamedFromId = (string)$journeyPartNode['renamedFrom'];
																	if (empty($_trainRenamedFromId) == false) {
																		$_trainRenamedFrom = $gvd->getTrainById($_trainRenamedFromId);
																		if (is_null($_trainRenamedFrom))
																			throw new ObjectNotFoundException($_trainRenamedFromId);
																		$journeyPart->setRenamedFrom($_trainRenamedFrom);
																	}
																}

																if ($isDefinedRenamedTo) {
																	$_trainRenamedToId = (string)$journeyPartNode['renamedTo'];
																	if (empty($_trainRenamedToId) == false) {
																		$_trainRenamedTo = $gvd->getTrainById($_trainRenamedToId);
																		if (is_null($_trainRenamedTo))
																			throw new ObjectNotFoundException($_trainRenamedToId);
																		$journeyPart->setRenamedTo($_trainRenamedTo);
																	}
																}

                                if (isset($journeyPartNode->car)) {
									$journeyPart->setCar((string)$journeyPartNode->car);
									
									$cars = explode(',', $journeyPart->getCar());
									foreach ($cars as $car) {
										$car = trim($car);
										$model->addCarToStatistics(new Car(md5($car), $car));
									}
								}
                                if (isset($journeyPartNode->note)) {
	                                $journeyPart->setNote((string)$journeyPartNode->note);
								}
																	
																	
                                if (isset($journeyPartNode->locomotive))
																	$journeyPart->setLocomotive((string)$journeyPartNode->locomotive);

                                $journey->addJourneyPart($journeyPart);
																
																if (!$journey->isPlanned()) {
																if (isset($journeyPartNode['ticket-full']) && $journey->isPlanned() == false) {
																	$model->getTicketById((string)$journeyPartNode['ticket-full'])->addDistance($journeyPart->getLength());
																}
																if (isset($journeyPartNode['ticket-part']) && $journey->isPlanned() == false) {
																	foreach (explode(';', $journeyPartNode['ticket-part']) as $tpsNode) {
                                    $tps = explode(':', $tpsNode);
                                    $model->getTicketById($tps[0])->addDistance((int)$tps[1]);
                                  }
																}
																}
                            }

                            $gvd->addJourney($journey);

														if (isset($journeyNode['ticket-full']) && $journey->isPlanned() == false) {
															$model->getTicketById((string)$journeyNode['ticket-full'])->addDistance($journey->getLength());
														}
                        }
                    }
                    $model->addGvd($gvd);
                }
            }
        }
    }

    /**
     *  <p>Object which parses all necessary data received from user and server and offers the by 
     *  appropriate getter methods.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    12th December 2005  Added getter for error view file name.
     *  @version    31st October 2005
     */
    class HttpRequest {
        // singleton object instance
        private static $instance;

				private $dataFileNames =
					array('raillog', 'raillog-plans', 'raillog-tomas-kubovec');
        private $requestedView;
				private $selectedDataFileName = 'raillog';

        /**
         *  <p>Hidden constructor. This class is intended to be a singleton. To get the instance call 
         *  <code>get()</code> method.</p>
         */
        private function __construct() {
            // parsing _GET, _POST, _SERVER into necessary variables
            $this->requestedView = 'views/_' . substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);

						if (is_array($_GET) && array_key_exists('dataFileName', $_GET)) {
							$this->selectedDataFileName = $_GET['dataFileName'];
						}
        }

        /**
         *  <p>Returns path to the data file which is source of the data for the application.</p>
         *
         *  @return string Path to the data file.
         */
        public function getDataFileName() {
					return $this->selectedDataFileName . '.xml';
				}

				public function getDataFileNames() {
					return $this->dataFileNames;
				} 

        /**
         *  <p>Returns path the view which has to be used to view the requested data.</p>
         *
         *  @return string Path to the view template file.
         */
        public function getRequestedView() { return $this->requestedView; }

        /**
         *  <p>Returns path to the view whis is used to display error messages.</p>
         *
         *  @return string Path to the error view template file.
         */
        public function getErrorView() { return 'views/_error.php'; }

        /**
         *  <p>Returns shared instance of this object.</p>
         *
         *  @return object HttpRequest Shared instance.
         */
        public static function get() {
            if(is_null(self::$instance)) self::$instance = new HttpRequest();
            return self::$instance;
        }
    }

    /**
     *  <p>Class which encapsulates some util methods for comparation. These methods should be moved to concrete objects
     *  and the class itself should be removed from the application.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    31st October 2005
     */
    class Utils {
        /**
         *  <p>Hidden constructor. This class is not allowed to be instantiate.</p>
         */
        private function __construct() {}

        /**
         *  <p>Methods used in comparation of any class implemented abstract class IdNamedObject. It compares
         *  values contained in the name property.</p>
         *
         *  @return int -1, O or 1 as a result of comparation of the name property. 
         */
        public static function idNamedObjectCompare(IdNamedObject &$a, IdNamedObject &$b) {
            return strnatcmp($a->getName(), $b->getName());
        }

				public static function idObjectCompare(IdObject &$a, &$b) {
					return strnatcmp($a->getId(), $b->getId());
				}

				public static function idObjectCompareReverse(IdObject &$a, &$b) {
					return -1 * self::idObjectCompare($a, $b);
				}
    }

    /**
     *	<p>Utils class use as a iterator for maps - collection called hashmaps or asociative arrays.</p>
     * 
     *	@author		Josef Petrak (jspetrak@gmail.com)
     *	@version 	19th November 2005 ("IC 501 Ostravan Bmpz" Edition)
     */
    class MapIterator {
        private $size;
        private $counter;
        private $collection;

        public function __construct($collection) {
            $this->size = count($collection);
            $this->counter = 0;
            $this->collection = array_values($collection);
        }

        public function current() { return $this->collection[$this->counter]; }

        public function hasNext() { return $this->counter < $this->size; }

        public function isLast() { return ($this->counter + 1) < $this->size; }

        public function next() { $this->counter++; }
    }

    /**
     *  <p>Exception which occurs when an existing object is for the second time stored in the model or its subpart.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    29th November 2005 ("IC 505 Jan Perner Bpee" Edition)
     */
    class ObjectAlreadyStoredException extends Exception {
        private $badObject;

        public function __construct($badObject) {
            parent::__construct('', 0);
            $this->badObject = $badObject;
        }

        protected function getBadObject() { return $this->badObject; }

        /**
         *  <p>Returns the string representation for the exception used to display it in the output.</p>
         *
         *  @return string String representation for this exception.
         */
        public function __toString() { return 'Objekt ' . $this->getBadObject()->__toString() . ' je definován dvakrát!'; }
    }

    /**
        <p>Exception which occurs when is required object without non-existing ID from the model.</p>

        @author     Josef Petrák (jspetrak@gmail.com)
        @version    15th December 2005 ("AMO" Edition)
     */
    class ObjectNotFoundException extends Exception {
        private $badId;

        /**
            <p>Constructor.</p>

            @param string $badId    ID of the non-existing object.
         */
        public function __construct($badId) {
            parent::__construct('', 0);
            $this->badId = $badId;
        }

        /**
            <p>Returns ID of the non-existing object.</p>

            @return string ID of the non-existing object
         */
        public function getBadId() { return $this->badId; }

        /**
            <p>Basic message written by the exception.</p>

            @return string  Textual representation of the exception
         */
        public function __toString() { return 'Objekt s ID ' . $this->getBadId() . ' nebyl v modelu nalezen!'; }
    }

?>