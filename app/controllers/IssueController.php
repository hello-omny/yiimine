<?php
class IssueController extends CommonController {
    public function actionIndex() {
        $this->render('index', array(
            'model' => $this->listing('Issue', array('project_id' => user()->getState('projectId'))),
        ));
    }

    public function actionCreate() {
        $project = $this->loadModel('Project', user()->getState('projectId'));
        $model = new Issue();
        $model->project_id = $project->id;
        $model->tracker_id = Issue::TRACKER_BUG;

        $this->_saveModel($model, 'добавлена');

        $this->render('create', array('model' => $model, 'project' => $project));
    }
    
    public function actionUpdate($id) {
        $project = $this->loadModel('Project', user()->getState('projectId'));
        $model = $this->loadModel('Issue', $id);
        
        $this->_saveModel($model, 'обновлена');
        
        $this->render('update', array('model' => $model, 'project' => $project));
    }
    
    public function actionDelete($id) {
        $this->delete('Issue', $id);
    }
    
    public function actionView($id) {
        $model = $this->loadModel('Issue', $id);

        $comments = new CActiveDataProvider('IssueComment', array(
            'criteria' => array(
                'condition' => 'issue_id = '.(int)$id,
            ),
            'sort' => array(
                'defaultOrder' => 'created_date DESC',
            ),
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));

        $this->render('view', array('model' => $model, 'comments' => $comments));
    }

    private function _saveModel($model, $actionText) {
        if(isset($_POST['Issue'])) {
            $model->attributes = $_POST['Issue'];
            if($model->save()) {
                user()->setFlash('success', "Задача #{$model->id} {$actionText}");
                $this->redirect(array('index'));
            }
        }
    }
}