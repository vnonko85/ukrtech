<?php

namespace console\services;

use console\models\Tree;

class GenerateTreeService
{
    public const POSITIONS = [1, 2]; 

    public function createChildren(Tree $node): void
    {
        if ($node->level >= 5) {
            return;
        }
        foreach (self::POSITIONS as $position) {
            $childNode = $node->getChild($position);
            if (null == $childNode) {
                $childNode = $this->createNode($node, $position);
            }

            $this->createChildren($childNode);
        }
    }

    public function createNode(Tree $parentNode, int $position): Tree
    {
        $newNode = new Tree();
        $newNode->parent_id = $parentNode->id;
        $newNode->position = $position;
        $newNode->level = $parentNode->level + 1;
        $newNode->path = trim($parentNode->path . '.' . $newNode->getNextId(), '.');
        $newNode->save();

        return $newNode;
    }

    public function moveNode(int $id, int $toId, int $position): void
    {
        if (!in_array($position, GenerateTreeService::POSITIONS)) {
            throw new \Exception("Not allowed position", 1);
        }

        $node = Tree::findOne($id);

        if (null == $node) {
            throw new \Exception("Node does not exist", 1);            
        }

        $toNode = Tree::findOne($toId);

        if (null == $toNode) {
            throw new \Exception("Parent node does not exist", 1);            
        }

        if (null != $toNode->getChild($position)) {
            throw new \Exception("Node with position already exist", 1);            
        }

        $oldPath = $node->path;

        $node->position = $position;
        $node->parent_id = $toNode->id;
        $node->level = $toNode->level + 1;
        $node->path = $toNode->path . '.' . $node->id;
        $node->save();

        \Yii::$app->db->createCommand("
            UPDATE tree SET
                path = REPLACE(path, '$oldPath', '{$node->path}') 
            WHERE path LIKE '$oldPath.%'
        ")->execute();
    }
}
