<?php

namespace matfish\Blogify\twigextensions;

use Craft;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\Tag;
use matfish\Blogify\Handles;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class BlogGlobalsTwigExtension extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals()
    {
        return [
            'blogify' => [
                'categories' => Category::find()->groupId($this->getCategoriesGroupId()),
                'usedCategories' => $this->getUsedCategories(),
                'tags' => Tag::find()->groupId($this->getTagsGroupId())
            ]
        ];
    }


    private function getTagsGroupId()
    {
        return \Craft::$app->cache->getOrSet(Handles::TAGS, function () {
            return \Craft::$app->tags->getTagGroupByHandle(Handles::TAGS)->id;
        }, 60 * 60 * 24 * 365);
    }

    private function getCategoriesGroupId()
    {
        return \Craft::$app->cache->getOrSet(Handles::CATEGORIES, function () {
            return \Craft::$app->categories->getGroupByHandle(Handles::CATEGORIES)->id;
        }, 60 * 60 * 24 * 365);
    }

    private function getUsedCategories()
    {
        $entries = Entry::find()->section(Handles::CHANNEL);

        return Category::find()
            ->groupId($this->getCategoriesGroupId())
            ->relatedTo($entries);
    }
}