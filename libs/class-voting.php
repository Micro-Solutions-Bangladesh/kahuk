<?php
if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

/**
 * 
 */
class KahukVoting {
    public $errors;

    public $linkId;
    public $userId;
    public $isPositiveVote;
    public $isUnVote;

    public $linkRecord;
    public $voteRecord;

    public $linkRecordNew;

    /**
     * Class construcotr
     */
    function __construct() {
        $this->errors = new Kahuk_Error();
    }

    /**
     * Initiate Voting Process
     */
    function init( $args ) {
        $this->linkId = $args['link_id'];
        $this->userId = $args['user_id'];
        $this->isPositiveVote = $args['is_positivevote'];
        $this->isUnVote = $args['is_unvote'];

        // $this->setLinkRecord();

        // Lets Check If the User Already Voted Previously
        $args = [
            'vote_link_id' => $this->linkId,
            'vote_user_id' => $this->userId,
        ];

        $this->voteRecord = kahuk_find_vote( $args );

        // Find If there is Invalid Action
        if ( empty( $this->voteRecord ) ) {
            // How could a user unvote, when he/she did not vote yet!!
            if ( $this->isUnVote ) {
                $this->errors->add( 'error', 'Unvote is not available for now!' );
                return;
            }
        } else {

            $wasVotePositive = ( 0 < intval( $this->voteRecord['vote_value'] ) );

            // User did vote positive, now trying to vote negative
            if ( $wasVotePositive && !$this->isPositiveVote ) {
                $this->errors->add( 'error', 'Require unvote first to make new negative vote!' );
                return;
            }

            // User did vote negative, now trying to vote positive
            if ( !$wasVotePositive && $this->isPositiveVote ) {
                $this->errors->add( 'error', 'Require unvote first to make new positive vote!' );
                return;
            }
            
        }

        // Identify We increase or decrease vote
        $isVoteIncrease = false;

        if ( $this->isPositiveVote && ! $this->isUnVote ) {
            $isVoteIncrease = true;
        } elseif ( ! $this->isPositiveVote && $this->isUnVote ) {
            $isVoteIncrease = true;
        }

        // Update karma in Link Table
        $this->linkRecord = kahuk_update_link_karma( $this->linkId, $isVoteIncrease, LINK_VOTE_KARMA );

        if ( false == $this->linkRecord ) {
            $this->errors->add( 'error', 'Unexpected error found!' );
            return;
        }

        $dataForVote = [];
        $dataForVote['vote_user_id'] = $this->userId;
        $dataForVote['vote_link_id'] = $this->linkId;
        $dataForVote['vote_value'] = $this->isPositiveVote ? 10 : -10;

        // Fresh New Vote as User Did Not Vote For This Post
        if ( ! $this->isUnVote && empty( $this->voteRecord ) ) {
            // Insert a new vote record
            kahuk_insert_vote( $dataForVote );

            // Increase user karma
            kahuk_update_user_karma( $isVoteIncrease, USER_VOTE_KARMA );


            // User did vote and want to Unvote
        } elseif ( $this->isUnVote && ! empty( $this->voteRecord ) ) {
            // Delete the vote record
            kahuk_delete_vote( $dataForVote );

            // Decrease user karma
            kahuk_update_user_karma( $isVoteIncrease, USER_VOTE_KARMA );
            
            // User did voted and still trying to vote
        } else {
            $this->errors->add( 'error', 'Record does not match to vote/unvote for this post!' );
            return;
        }
    }

}
















// include(KAHUK_LIBS_DIR.'link.php');

// class LinkTotal extends Link {
//     function remove_vote($user=0, $value=10) {
//         if (parent::remove_vote($user, $value))
//         {

//             $vote = new Vote;
//             $vote->type='links';
//             $vote->link=$this->id;
//             if(Voting_Method == 2){
//                 $this->votes=$vote->rating("!=0");
//                 $this->votecount=$vote->count("!=0");
//                 $this->reports = $this->count_all_votes("<0");
//             }
//             else
//             {
//                 $this->reports = $this->count_all_votes("<0");
//                 $this->votes   = $vote->count()-$this->reports;
//             }
//             $this->store_basic();
            
//             $vars = array('link' => $this);
//             check_actions('link_remove_vote_post', $vars);

//             return true;
//         }
//         return false;
//     }
    
//     function insert_vote($user=0, $value=10) {
//         if (parent::insert_vote($user, $value))
//         {
//             $vote = new Vote;
//             $vote->type='links';
//             $vote->link=$this->id;
//             if(Voting_Method == 2){
//                 $this->votes=$vote->rating("!=0");
//                 $this->votecount=$vote->count("!=0");
//                 $this->reports = $this->count_all_votes("<0");
//             }
//             else
//             {
//                 $this->reports = $this->count_all_votes("<0");
//                 $this->votes   = $vote->count()-$this->reports;
//             }
//             $this->store_basic();
//             $this->check_should_publish();
            
//             $vars = array('vote' => $this);
//             check_actions('link_insert_vote_post', $vars);		
            
//             return true;
//         }
//         return false;
//     }
// }
