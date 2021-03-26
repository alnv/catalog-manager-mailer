<?php

namespace CatalogManager\Mailer;

use CatalogManager\CatalogController as CatalogController;

class Cronjob extends CatalogController {


    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
    }


    public function mailer() {

        if ( !$this->Database->tableExists( 'tl_mailer' ) ) return null;

        $objMailers = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE in_progress = "1"' )->execute();

        if (!$objMailers->numRows) return null;

        while ($objMailers->next()) {
            $arrParameters = $objMailers->row();
            if (!$this->Database->tableExists( $objMailers->tablename)) {
                $this->setState('failed', $arrParameters['id']);
                continue;
            }

            $objMailer = new Mailer( $arrParameters );
            $objMailer->send();
        }
    }


    public function reminder() {

        if ( !$this->Database->tableExists( 'tl_reminder' ) ) return null;

        $objReminders = $this->Database->prepare( 'SELECT * FROM tl_reminder ' )->execute();

        if ( !$objReminders->numRows ) return null;

        $intCurrentTime = time();
        $intDayInterval = 86400;

        $arrIntervals = [

            'once' => $intDayInterval,
            'daily' => $intDayInterval,
            'weekly' => $intDayInterval * 7,
            'semimonthly' => $intDayInterval * 14,
            'monthly' => $intDayInterval * 30,
            'quarter' => ( $intDayInterval * 30 ) * 4,
            'yearly' => ( $intDayInterval * 30 ) * 12
        ];

        while ( $objReminders->next() ) {

            $this->executeReminderByInterval( $objReminders->interval, $objReminders, $intCurrentTime, $arrIntervals );
        }
    }


    protected function setState( $strState, $strId ) {

        $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

            'state' => $strState,
            'in_progress' => '',
            'start_at' => '',
            'end_at' => '',
            'offset' => 0

        ])->execute( $strId );
    }


    protected function executeReminderByInterval( $strIntervalType, $objReminder, $intCurrentTime, $arrIntervals ) {

        if ( !$objReminder->last_execution ) {

            if ( $objReminder->first_execution >= $intCurrentTime ) {

                $this->executeReminder( $objReminder );
            }

            return null;
        }

        $intInterval = time() - $objReminder->last_execution;

        switch ( $strIntervalType ) {

            case 'once':

                //

                break;

            case 'daily':

                if ( $intInterval >= $arrIntervals['daily'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;

            case 'weekly':


                if ( $intInterval >= $arrIntervals['weekly'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;

            case 'semimonthly':

                if ( $intInterval >= $arrIntervals['semimonthly'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;

            case 'monthly':

                if ( $intInterval >= $arrIntervals['monthly'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;

            case 'quarter':

                if ( $intInterval >= $arrIntervals['quarter'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;

            case 'yearly':

                if ( $intInterval >= $arrIntervals['yearly'] ) {

                    $this->executeReminder( $objReminder );
                }

                break;
        }
    }


    protected function executeReminder( $objReminder ) {

        $objMailer = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE id = ?' )->limit(1)->execute( $objReminder->mailer_id );

        if ( !$objMailer->in_progress ) {

            $this->Database->prepare('UPDATE tl_mailer %s WHERE id = ?')->set([

                'reminder_id' => $objReminder->id,
                'post' => serialize( [] ),
                'start_at' => time(),
                'in_progress' => '1',
                'state' => 'active',
                'offset' => 0

            ])->execute( $objReminder->mailer_id );
        }

        else {

            $this->Database->prepare('INSERT INTO tl_mailer_queue %s')->set([

                'tstamp' => time(),
                'post' => serialize( [] ),
                'reminder_id' => $objReminder->id,
                'mailer_id' => $objReminder->mailer_id

            ])->execute();
        }

        $this->Database->prepare('UPDATE tl_reminder %s WHERE id = ?')->set([

            'last_execution' => time()

        ])->execute( $objReminder->id );
    }
}