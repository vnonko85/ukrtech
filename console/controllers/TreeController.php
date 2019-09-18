<?php

namespace console\controllers;

use yii\console\Controller;
use console\models\Tree;
use console\services\GenerateTreeService;

class TreeController extends Controller
{
    private $service;

    public function __construct(string $id, $module, $config = [], GenerateTreeService $service)
    {
        $this->service = $service;

        parent::__construct($id, $module, $config);
    }

    public function actionCreateNode(int $parentId, int $position): void
    {
        if (!in_array($position, GenerateTreeService::POSITIONS)) {
            throw new \Exception("Not allowed position", 1);
        }

        $parentNode = Tree::findOne($parentId);

        if (null == $parentNode) {
            throw new \Exception("Parent node does not exist", 1);
        }

        if (null != $parentNode->getChild($position)) {
            throw new \Exception("Node with position already exist", 1);
        }

        $this->service->createNode($parentNode, $position);
    }

    public function actionFill(): void
    {
        $rootNode = Tree::findOne(1);
        $this->service->createChildren($rootNode);
    }

    public function actionGetChildren(int $id): void
    {
        $node = Tree::findOne($id);
        
        if (null == $node) {
            throw new \Exception("Node does not exist", 1);
        }

        $children = Tree::find()->where(['like', 'path', $node->path . '.%', false])->all();
        $this->printNodes($children);
    }

    public function actionGetParents(int $id): void
    {
        $node = Tree::findOne($id);

        if (null == $node) {
            throw new \Exception("Node does not exist", 1);
        }

        $paths = [];
        $currentPath = $node->path;

        do {
            preg_match('/(.*)\.\d+$/', $currentPath, $matches);
            $currentPath = $paths[] = $matches[1];
        } while (strpos($currentPath, '.') !== false);

        $parents = Tree::find()->where(['in', 'path', $paths])->all();
        $this->printNodes($parents);
    }

    public function actionMoveNode(int $id, int $toId, int $position): void
    {
        $this->service->moveNode($id, $toId, $position);
    }

    private function printNodes(array $nodes): void
    {
        foreach ($nodes as $node) {
            printf("id: %u | path: %s | parent: %u | position: %u | level: %u\n",
                $node->id,
                $node->path,
                $node->parent_id,
                $node->position,
                $node->level
            );
        }
    }
}
