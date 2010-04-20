<?php

    // abstract classes

    /**
     *  <p>Core abstract model class which represents all objects that are identified by a string ID.
     *  It defines also constructor, getter for ID, method to analyze equality and string conversion
     *  methods so that concrete classes which extends this class needn't (re)implement it.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    31st October 2005
     */
    abstract class IdObject {
        private $id;

        /**
         *  <p>Constructor of any IdObject. Sets up its ID by value given as a parameter.</p>
         *
         *  @param $id ID of the object
         */
        public function __construct($id) { $this->id = $id; }

        /**
         *  <p>Getter method for the ID property.</p>
         *
         *  @return string Value of the ID property
         */
        public function getId() { return $this->id; }

        /**
         *  <p>Returns string representation of any IdObject. Inf fact It returns the value
         *  of the ID property.</p>
         *
         *  @return string Character string representating instance of the object
         */
        public function __toString() { return $this->id; }

        /**
         *  <p>Methods which analyses whether the given object is equal to the first object. It compares
         *  the datatype first (if is an object given as a parameter) and than compares the values of the
         *  ID property.</p>
         *
         *  @return boolean States if the second object is equal with the first one
         */
        public function equals(IdObject $that) {
            if ($this === $that) return true;
            return $this->getId() == $that->getId();
        }
    }

    /**
     *  <p>Abstract model class which represents all object the are identified by a string ID and have also
     *  a textual string representing their name. It defines also constructor, getter for name, method to 
     *  analyze equality string conversion method so that concrete classes which extends this class needn't
     *  (re)implement it.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    1st November 2005
     */
    abstract class IdNamedObject extends IdObject {
        private $name;

        /**
         *  <p>Constructor. It sets up the name property with the value given as a parameter and calls
         *  super class constructor to set up the ID property.</p>
         *
         *  @param id   Property ID value
         *  @param name Property name value
         */
        public function __construct($id, $name) {
            parent::__construct($id);
            $this->name = $name;
        }

        /**
         *  <p>Getter method for the name property.</p>
         *
         *  @return string Value of the name property
         */
        public function getName() { return $this->name; }

        /**
         *  <p>Returns string representation of any IdNamedObject. Inf fact It returns the value
         *  of the name property.</p>
         *
         *  @return string Character string representating instance of the object
         */
        public function __toString() { return $this->name; }

        /**
         *  <p>Methods which analyses whether the given object is equal to the first object. It compares
         *  the datatype first (if is an object given as a parameter), than the value of the ID property
         *  by the super implementation of this method and than compares the values of the name property.</p>
         *
         *  @return boolean States if the second object is equal with the first one
         */
        public function equals(IdNamedObject $that) {
            if ($this === $that) {
                return true;            
            } else {
                return (parent::equals($that)) ? ($this->getName() == $that->getName()) : false;
            }
        }
    }

    /**
     *  <p>Abstract model class which represents any object identified by a start and end date and time which
     *  together identify validity of the resource. It defines all necessary getters, setters, internal logic
     *  to transform used textual notation for date and time into the UNIX timestamp.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmai.com)
     *  @version    1st November 2005
     */
    abstract class TimedObject extends IdObject {
        // constants used by internal logic
        const START = '0';
        const END = '59';
        // object properties properties
        private $end;
        private $start;

        /**
         *  <p>Constructor. In fact it does nothing but it is defined to see that it is avaliable and the
         *  right way how to make an instance of any TimedObject.</p>
         */
        public function __construct($id) {
            parent::__construct($id);
        }

        /**
         *  <p>Method whic transform textual notation of date and time into the UNIX timestamp. It used in the
         *  setters.</p>
         *
         *  @param dt Date and time in the notation D.M.YYYY H:MM (without leading zeros in day,month and hour).
         *  @return int Date and time int the UNIX timestamp
         */
        protected static function parseDateTime($dt, $secondMode = '0') {
            $parts = explode(' ', $dt);
            $dateparts = explode('.', $parts[0]);
            $timeparts = explode(':', $parts[1]);

            return mktime( $timeparts[0], $timeparts[1], $secondMode, $dateparts[1], $dateparts[0], $dateparts[2] );
        }

        /**
         *  <p>Returns value represeting end date and time of validitiy of the object.</p>
         *
         *  @return int UNIX stamp of the end of duration of the validity
         */
        public function getEnd() { return $this->end; }

        /**
         *  <p>Returns value represeting start date and time of validitiy of the object.</p>
         *
         *  @return int UNIX stamp of the start of duration of the validity
         */
        public function getStart() { return $this->start; }    

        /**
         *  <p>Sets up the value for the end date of the duration.</p>
         *
         *  @param strind Date and time in the notation D.M.YYYY H:MM.
         */
        public function setEnd($dt) { $this->end = TimedObject::parseDateTime($dt, TimedObject::START); }

        /**
         *  <p>Sets up the value for the start date of the duration.</p>
         *
         *  @param strind Date and time in the notation D.M.YYYY H:MM.
         */
        public function setStart($dt) { $this->start = TimedObject::parseDateTime($dt, TimedObject::START); }    
    }

    // concrete object classes

		class Ticket extends IdNamedObject {
			private $distance = 0;
			private $price = 0;
			private $currency = '';
			private $archived = false;
			
			public function getPrice() { return $this->price; }
			public function setPrice($price) { $this->price = $price; }
			
			public function getCurrency() { return $this->currency; }
			public function setCurrency($currency) { $this->currency = $currency; }

			public function getDistance() { return $this->distance; }
			public function addDistance($distance) {
				if (is_int($distance) && $distance > 0) {
					$this->distance += $distance;
				} else {
					throw new Exception('Illegal argument - distance: '.$distance.' for object Ticket['.$this->getId().']');
				}
			}

			public function isArchived() { return $this->archived; }
			public function setArchived($archived) { $this->archived = $archived; }
		}
		
   	class Event extends IdNamedObject {
		private $journeys = array();
		private $_distance = 0;
		
		public function addJourney(Journey $journey) {
			$this->_distance += $journey->getDistance();
			$this->journeys[] = $journey;
		}
		
		public function getDistance() { return $this->_distance; }
		public function getJourneys() { return $this->journeys; }
	}

	/**
	 *	<p>Object defines any country. ID is an ISO-code
	 *	and name is the concrete localized name 
	 *	of the country.</p>
	 *
	 *	@author		Josef Petrak (jspetrak@gmail.com)
	 *	@version	2006-12-16		Class created by extending IdNamedObject, added cities.
	 */
	class Country extends IdNamedObject {
		private $cities = array();
		
		/**
		 *	<p>Adds new city into the country. Duplicities are not allowed.</p>
		 */
		public function addCity(&$city) {
			if (in_array($city, $this->cities) == false) {
				$this->cities[] = $city;
			}
		}
		
		/**
		 *	<p>Returns list of all cities in the country.</p>
		 */
		public function getCities() { return $this->cities; }
	}

	class City extends IdNamedObject {
		private $country;
		private $stations = array();

		public function __construct(&$country) {
			$this->country = $country;
		}

		public function addStation(&$station) {
			
		}

		public function getCountry() { return $this->country; }
		public function getStations() { return $this->stations; }
	}

    /**
     *  <p>Object representing the traveller - person wichi travelled by these trains and who is the statistic about.
     * 	It contains some contact information and also offer the RDF represnetation for the person (using
     * 	application-defined schema).</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    13th December 2005  Changes made according to the new schema - multiple values
     *              for contact properties.
     *  @version    28th November 2005 ("IC 142 Odra Bpee" Edition)
     */
    class Traveller extends IdNamedObject {
        private $aim;
        private $icq;
        private $msn;
        private $nick;
        private $email;
        private $jabber;
        private $weblog;
        private $homepage;

        public function getAim() { return $this->aim; }
        public function getIcq() { return $this->icq; }
        public function getMsn() { return $this->msn; }
        public function getNick() { return $this->nick; }
        public function getEmail() { return $this->email; }
        public function getJabber() { return $this->jabber; }
        public function getWeblog() { return $this->weblog; }
        public function getHomepage() { return $this->homepage; }

        public function getEmailAsIterator() { return new ArrayIterator($this->getEmail()); }
        public function getWeblogAsIterator() { return new ArrayIterator($this->getWeblog()); }
        public function getHomepageAsIterator() { return new ArrayIterator($this->getHomepage()); }

        /**
         *  <p>Adds new AIM account into the object of the traveller.</p>
         */
        public function setAim($aim) {
            if (is_null($this->aim)) $this->aim = array();
            $this->aim[] = $aim;
        }

        /**
         *  <p>Adds new ICQ account into the object of the traveller.</p>
         */
        public function setIcq($icq) {
            if (is_null($this->icq)) $this->icq = array();
            $this->icq[] = $icq;
        }

        /**
         *  <p>Adds new MSN account into the object of the traveller.</p>
         */
        public function setMsn($msn) {
            if (is_null($this->msn)) $this->msn = array();
            $this->msn[] = $msn;
        }

        public function setNick($nick) { $this->nick = $nick; }

        /**
            <p>Adds new e-mail account into the object of the traveller. Duplicities are not allowed.</p>

            @param string $email    E-mail account
         */
        public function setEmail($email) {
            if (is_null($this->email)) $this->email = array();
            if (!in_array($email, $this->email)) {
                $this->email[] = $email;
            }
        }

        /**
            <p>Adds new Jabber account into the object of the traveller. Duplicities are not allowed.</p>

            @param string $jabber   Jabber account.
         */
        public function setJabber($jabber) {
            if (is_null($this->jabber)) $this->jabber = array();
            if (!in_array($jabber, $this->jabber)) {
                $this->jabber[] = $jabber;
            }
        }

        /**
            <p>Adds new Jabber account into the object of the traveller. Duplicities
            are not allowed.</p>

            @param string $jabber   Jabber account.
         */
        public function setWeblog($weblog) {
            if (is_null($this->weblog)) $this->weblog = array();
            if (!in_array($weblog, $this->weblog)) {
                $this->weblog[] = $weblog;
            }
        }

        /**
            <p>Adds new Jabber account into the object of the traveller. Duplicities
            are not allowed.</p>

            @param string $homepage   Jabber account.
         */
        public function setHomepage($homepage) {
            if (is_null($this->homepage)) $this->homepage = array();
            if (!in_array($homepage, $this->homepage)) {
                $this->homepage[] = $homepage;
            }
        }
    }

	class Station extends IdNamedObject {}

	/**
	 *	<p>Represents one train. Each train has system ID, full name
	 *	including category, number and name, and may have a note
	 *	about his road.</p>
	 *
	 *	@author		Josef Petrak (jspetrak@gmail.com)
	 *	@version	2006-12-15		Added noteRoad attribute
	 */
	class Train extends IdNamedObject {
		private $noteRoad;
		private $orderName;

		/**
		 *	<p>Returns the note about the road. It is null if not specified.</p>
		 */
		public function getNoteRoad() { return $this->noteRoad; }

		/**
		 *	<p>Sets up note about the road of the train.</p>
		 *
		 *	@param	string	$noteRoad		New note about the road
		 */
		public function setNoteRoad($noteRoad) { $this->noteRoad = $noteRoad; }
	}

    /**
     *	<p>Gvd represents a document containg train schedule valid for appropriate period called GVD. The object
     * 	contains all journeys from this period and stores train objects because they are defined only for one period
     * 	and then can change or be canceled.</p>
     * 
     *	@author		Josef Petrak (jspetrak@gmail.com)
     *	@version 	29th November 2005 ("IC 504 Jan Perner Bpee" Edition)
     */
    class Gvd extends TimedObject {
        private $trains = array();
        private $journeys = array();

        // cache properties
        private $_appearedTrains;
        private $_appearedStations;
        private $_statTrains;

        /**
         *	<p>Searches for a train using a given ID.</p>
         *
	     * 	@param string $id ID for the searched train
	     *	@return object Train Train with given ID
         */
        public function getTrainById($id) { if(array_key_exists($id, $this->trains)) return $this->trains[$id]; }

        /**
         * 	<p>Returns list of trains stored in the GVD object.</p>
         *
         * 	@return array List of trains
         */
        public function getTrains() { return $this->trains; }
        
        /**
         *	<p>Return list of journeys stored in the GVD object.</p>
         *
         * 	@return array List of journeys
         */
        public function getJourneys() { return $this->journeys; }

        /**
         *  <p>Adds new train into the GVD. If is the train already stored, new exception is thrown.</p>
         *
         *  @param  object Train New train object.
         *  @throws ObjectAlreadyStoredEception Exception is thrown if the object is already stored.
         */
        public function addTrain(Train $train) {
            if (!array_key_exists($train->getId(), $this->trains)) {
            	$this->trains[$train->getId()] = $train;
            } else {
            	throw new ObjectAlreadyStoredException($train);
            }
        }

        /**
         *  <p>Adds new journey unto the GVD.</p>
         *
         *  @param object Journey New journey object.
         */
        public function addJourney(Journey $journey) { $this->journeys[] = $journey; }

        /**
         *  <p>Returns complete distance of all journeys done in this GVD.</p>
         *
         *  @return int Complete distance.
         */
        public function getLength() {
            $res = 0;
            foreach ($this->journeys as $journey) {
							if ($journey->isPlanned()) {
								continue;
							} else {
								$res += $journey->getLength();
							}
            }
            return $res;
        }

        public function getAppearedStationsAsMap() {
            if (is_null($this->_appearedTrains)) {
            $this->apperaredStations = array();
                foreach ($this->journeys as $journey) {
                    if ($journey->isPlanned()) continue;
                    foreach ($journey->getJourneyParts() as $journeypart) {
                        if (is_null($journeypart->getCarOrTrainFrom()) && is_null($journeypart->getRenamedFrom()) && !in_array($journeypart->getStartStation(), $this->apperaredStations)) {
                            $this->apperaredStations[] = $journeypart->getStartStation();
                        }
                        if (is_null($journeypart->getCarOrTrainTo()) && is_null($journeypart->getRenamedTo()) && !in_array($journeypart->getEndStation(), $this->apperaredStations)) {
                            $this->apperaredStations[] = $journeypart->getEndStation();
                        }
                    }
                }
                usort($this->apperaredStations, array('Utils', 'idNamedObjectCompare'));
            }
            return $this->apperaredStations;
        }

        /**
            <p>Returns statistics of appearance of the stations as an MapIterator object.</p>

            @return object MapIterator Iterator of statistic data.
         */
        public function getAppearedStationsAsMapIterator() {
            return new MapIterator($this->getAppearedStationsAsMap());
        }

        public function getAppearedTrainsAsMap() {
            if (is_null($this->_appearedTrains)) {
                $this->_appearedTrains = array();
                foreach ($this->journeys as $journey) {
                    if ($journey->isPlanned()) continue;
                    foreach ($journey->getJourneyParts() as $journeypart) {
                        if (!in_array($journeypart->getTrain(), $this->_appearedTrains)) {
                            $this->_appearedTrains[] = $journeypart->getTrain();
                        }
                    }
                }
                usort($this->_appearedTrains, array('Utils', 'idNamedObjectCompare'));
            }
            return $this->_appearedTrains;
        }

        /**
            <p>Returns statistics of uasge of the trains as an MapIterator object.</p>

            @return object MapIterator Iterator of statistic data.
         */
        public function getAppearedTrainsAsMapIterator() {
            return new MapIterator($this->getAppearedTrainsAsMap());
        }

        public function getTrainStatAsList() {
            if (is_null($this->_statTrains)) {
                $this->_statTrains = array();
                
                foreach ($this->journeys as $journey) {
                  $toCheck = array();
                
                    if ($journey->isPlanned()) continue;
                    foreach ($journey->getJourneyParts() as $journeypart) {
                        
                            if (array_key_exists($journeypart->getTrain()->getId(), $toCheck)) {
                              $toCheck[$journeypart->getTrain()->getId()]->increment();
                            } else {
                              $toCheck[$journeypart->getTrain()->getId()] = new StatCountTuple($journeypart->getTrain());
                            }                
                          
                        
                    }
                    
                    //echo('<pre>'.print_r($toCheck, true).'</pre><br /><pre>'.print_r($this->_statTrains, true).'</pre><hr />');
                    
                    // Check trains to ne checked
                    foreach ($toCheck as $trainId => $train) {
                      
                        if (!array_key_exists($trainId, $this->_statTrains)) {
                            $this->_statTrains[$trainId] = new StatCountTuple($train->getObject());
                        } else {
                          $this->_statTrains[$trainId]->increment();
                        }
                      
                    }
                }
                
                usort($this->_statTrains, array('StatCountTuple', 'compare'));
            }
            return $this->_statTrains;
        }
    }

    /**
     *  <p>One journey which consists of one or more parts which are stored within the object.</p>
     *
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    9th December 2005 ("IZI 238" Edition)
     */
    class Journey {
        private $planned = false;
        private $journeyParts = array();

        // cache properties
        private $_duration;
				private $_length;

        /**
         *  <p>Constructor creating a new journey object - it is just a container for its parts.</p>
         *
         *  @param boolean $planned States if the journey is planned or already happened.
         */
        public function __construct($planned) {
            if (is_bool($planned)) {
                $this->planned = $planned;
            }            
        }

        /**
         *  <p>States if the journey already happened in the past or if is only planned and thus
         *  can be canceled. It is up to discussion whether count planned journeys in the statistics?!?</p>
         *
         *  @return boolean True if journey is planned
         */
        public function isPlanned() { return $this->planned; }

        /**
         *  Returns collection of all parts of the journey.
         *
         *  @return array Collection of JourneyPart objects.
         */
        public function getJourneyParts() { return $this->journeyParts; }

        /**
         *  <p>Adds new part of the journey into the container - Journey. The parts are not sorted. They are stored
         *  in the order in which they were stored.</p>
         *
         *  @param object JourneyPart Part of the journey.
         */
        public function addJourneyPart(JourneyPart $journeyPart) { $this->journeyParts[] = $journeyPart; }

        /**
         *  <p>Returns start station of the whole journey.</p>
         *
         *  @return object Station Start station of the journey.
         */
        public function getStartStation() { return $this->journeyParts[0]->getStartStation(); }

        /**
         *  <p>Returns end station of the whole journey.</p>
         *
         *  @return object Station End station of the journey.
         */
        public function getEndStation() { return $this->journeyParts[count($this->journeyParts)-1]->getEndStation(); }

        /**
         *  <p>Returns the computed value of duration of this journey.</p>
         *
         *  @return int Duration of this part of the journey.
         */
        public function getDurationInSec() {
            if (is_null($this->_duration)) {
                $this->_duration = $this->journeyParts[count($this->journeyParts) - 1]->getEnd() - $this->journeyParts[0]->getStart();
            }
            return abs($this->_duration);
        }

				public function getLength() {
					if (is_null($this->_length)) {
						$this->_length = 0;
						foreach ($this->journeyParts as $journeyPart) {
							$this->_length += $journeyPart->getLength();
						}
					}
					return $this->_length;
				}
    }

    /**
        <p>Object representing a part of the journey.</p>

        @author     Josef Petrak (jspetrak@gmail.com)
        @version    13th December   Added properties and getters for car, note and locomotive elements.
        @version    29th November ("IC 505 Jan Perner Bpee" Edition)
     */
    class JourneyPart extends TimedObject {
        private $train;
        private $length;
        private $endStation;
        private $startStation;

        // Notes
        private $car;
        private $note;
        private $locomotive;

				// Direct cars and name changes
				private $carOrTrainFrom;
				private $carOrTrainTo;
				private $renamedFrom;
				private $renamedTo;

        private static $idCounter = 0;

        public function __construct() {
            parent::__construct(++self::$idCounter);
        }

        /**
         *  <p>Returns the object of the train which the traveller travelled by.</p>
         *
         *  @return object Train Used train
         */
        public function getTrain() { return $this->train; }
        public function getLength() { return $this->length; }
        public function getEndStation() { return $this->endStation; }
        public function getStartStation() { return $this->startStation; }
        public function getCar() { return $this->car; }
        public function getNote() { return $this->note; }
        public function getLocomotive() { return $this->locomotive; }
				public function getCarOrTrainFrom() { return $this->carOrTrainFrom; }
				public function getCarOrTrainTo() { return $this->carOrTrainTo; }
				public function getRenamedFrom() { return $this->renamedFrom; }
				public function getRenamedTo() { return $this->renamedTo; }

        public function setTrain(Train &$train) { $this->train = $train; }
        public function setLength($length) { $this->length = $length; }
        public function setEndStation(Station &$station) { $this->endStation = $station; }
        public function setStartStation(Station &$station) { $this->startStation = $station; }
        public function setCar($car) { $this->car = $car; }
        public function setNote($note) { $this->note = $note; }
        public function setLocomotive($locomotive) { $this->locomotive = $locomotive; }
				public function setCarOrTrainFrom(Train &$carOrTrainFrom) { $this->carOrTrainFrom = $carOrTrainFrom; }
				public function setCarOrTrainTo(Train &$carOrTrainTo) { $this->carOrTrainTo = $carOrTrainTo; }
				public function setRenamedFrom(Train &$renamedFrom) {
					$this->renamedFrom = $renamedFrom;
				}
				public function setRenamedTo(Train &$renamedTo) {
					$this->renamedTo = $renamedTo;
				}
    }

	class Car extends IdNamedObject {}

    /**
     *  @author     Josef Petrak (jspetrak@gmail.com)
     *  @version    28th November 2005 ("IC 142 Odra Bpee" Edition)
     */
    class ApplicationModel {
        // singleton instance
        private static $instance;

        private $traveller;
        private $gvds = array();
        private $stations = array();
		private $tickets = array();

		private $statisticsCars = array();

        // cache properties
        private $_length;
        private $_duration;

        /**
         *  <p>Hidden constructor which supports the implementation of the Singleton design pattern.
         *  To instantiate the object use the method <code>getInstance()</code>.</p>
         */
        private function __construct() {}

		public function getStatisticsCars() { return $this->statisticsCars; }
		public function addCarToStatistics(Car $car) {
			if (array_key_exists($car->getId(), $this->statisticsCars)) {
				$this->statisticsCars[$car->getId()]->increment();
			} else {
				$this->statisticsCars[$car->getId()] = new StatCountTuple($car);
			}
		}

        public function getTraveller() { return $this->traveller; }

        public function getGvds() {
					usort($this->gvds, array('Utils', 'idObjectCompareReverse'));
					return $this->gvds;
				}

				public function getTickets() { return $this->tickets; }
				public function addTicket(Ticket $ticket) {
					if (array_key_exists($ticket->getId(), $this->tickets)) {
						throw new Exception('Ticket of ID '.$ticket->getId().' already exists');
					}	else {
						$this->tickets[$ticket->getId()] = $ticket;
					}
				}
				public function getTicketById($ticketId) {
					if (array_key_exists($ticketId, $this->tickets)) {
						return $this->tickets[$ticketId];
					} else {
						throw new Exception('Ticket of ID '.$ticketId.' not found');
					}
				}

        public function getStationById($id) { if (array_key_exists($id, $this->stations)) return $this->stations[$id]; }

        public function setTraveller(Traveller $traveller) { $this->traveller = $traveller; }
        public function addGvd(Gvd $gvd) { $this->gvds[] = $gvd; }
        public function addStation(Station $station) {
            if (!array_key_exists($station->getId(), $this->stations)) {
                $this->stations[$station->getId()] = $station;
            } else {
                throw new ObjectAlreadyStoredException($station);
            } 
        }

        /**
         *  <p>Returns computed value of complete distance all of journeys made by train and loggend in this 
         *  application. The value is cache after first call of this method.</p>
         *
         *  @return int Complete distance of all journeys.
         */
        public function getLength() {
            if (is_null($this->_length)) {
                $this->_length = 0;
                foreach ($this->gvds as $gvd) {
                    $this->_length += $gvd->getLength();
                }
            }
            return $this->_length;
        }

        /**
         *  <p>Returns the computed value of duration of this journey.</p>
         *
         *  @return int Duration of this part of the journey.
         */
        public function getDurationInSec() {
            if (is_null($this->_duration)) {
                $this->_duration = 0;
                foreach ($this->getGvds() as $gvd) {
                    foreach ($gvd->getJourneys() as $journey) {
                        if ($journey->isPlanned()) continue;
                        $this->_duration += $journey->getDurationInSec();
                    }
                }
            }
            return $this->_duration;
        }

        public function getStatJourneysAsList() {      
            $stat = array();
            foreach ($this->getGvds() as $gvd) {
                foreach ($gvd->getJourneys() as $journey) {
                    if ($journey->isPlanned()) continue;
                    $key = $journey->getStartStation()->getId().$journey->getEndStation()->getId();
                    if (!array_key_exists($key, $stat)) {
                        $stat[$key] = new StatCountTriple($journey->getStartStation(), $journey->getEndStation());  
                    } else {
                        $stat[$key]->increment();
                    }
                }
            }
            usort($stat, array('StatCountTriple', 'compare'));
            return $stat;
        }

        /**
         *  @return object ApplicationModel Shared instance
         */
        public static function &getInstance() {
            if (is_null(self::$instance)) self::$instance = new ApplicationModel();
            return self::$instance;
        }
    }

?>
