<?php
namespace App\Controller\Posts;

use App\Controller\AppController;
use App\Exception\PostNotFoundException;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 *
 * @method \App\Model\Entitgty\Comment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{
    public $paginate = [
        'limit' => 10,
        'order' => [
            'Posts.created' => 'desc'
        ]
    ];
    
    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetches Comments of the given post id
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->request->allowMethod('GET');
        $postId = $this->request->getParam('id');
        if ( ! $this->Comments->Posts->exists(['id' => $postId])) {
            throw new PostNotFoundException($postId);
        }
        $comments = $this->paginate($this->Comments->fetchByPost($postId));
        $this->APIResponse->responseData($comments);
    }

    /**
     * [POST]
     * [PRIVATE]
     * 
     * Adds a comment to a post
     *
     * @return \Cake\Http\Response|null
     * 
     * @throws \App\Exception\PostNotFoundException
     * @throws \App\Exception\ValidationErrorsException
     * @throws \Cake\Http\Exception\InternalErrorException
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $postId = $this->request->getParam('id');
        $requestData = $this->request->getData();
        $requestData['user_id'] = $this->Auth->user('id');
        $comment = $this->Comments->addComment($postId, $requestData);

        return $this->APIResponse->responseCreated($comment);
    }
}
