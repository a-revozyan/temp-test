<?php
namespace backapi\models\forms\newsForms;

use common\models\News;
use common\models\NewsTag;
use common\models\Tag;
use yii\base\Model;


class UpdateNewsForm extends Model
{
    public $id;
    public $title_uz;
    public $title_ru;
    public $title_en;
    public $short_info_uz;
    public $short_info_ru;
    public $short_info_en;
    public $image_uz;
    public $image_ru;
    public $image_en;
    public $body_uz;
    public $body_ru;
    public $body_en;
    public $status;
    public $is_main;
    public $tag_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'title_uz', 'title_ru', 'short_info_uz', 'short_info_ru',
                'body_uz', 'body_ru', 'status', 'is_main'], 'required'],
            [['title_uz', 'title_ru', 'title_en', 'short_info_uz', 'short_info_ru', 'short_info_en',
                'body_uz', 'body_ru', 'body_en'], 'string'],
            [['title_uz', 'title_ru', 'title_en', 'short_info_uz', 'short_info_ru', 'short_info_en',], 'string', 'max' => 1000],
            [['image_uz', 'image_ru', 'image_en'], 'file', 'skipOnEmpty' => true],
            [['status', 'is_main'], 'integer'],
            [['is_main'], 'in', 'range' => [0,1]],
            [['status'], 'in', 'range' => News::STATUS],
            [['tag_ids'], 'each', 'rule' => ['integer']],
            [['tag_ids'], 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['tag_ids' => 'id']]],
        ];
    }

    public function save()
    {
        $news = News::findOne($this->id);
        $news->setAttributes($this->attributes);
        $news->updated_at = date('Y-m-d H:i:s');
        $news->save();

        foreach (['uz', 'ru', 'en'] as $lang) {
            $image_attr = "image_" . $lang;
            $image = $this->{$image_attr};
            if (array_key_exists($image_attr, $_FILES))
            {
                if (!empty($news->{$image_attr}))
                    unlink(News::images_folder() . $news->{$image_attr});
                $news->{$image_attr} = null;
                if (!empty($image))
                {
                    $news->{$image_attr} = $news->id ."news_$lang" . date('__Y_m_d_H_s_i') . "." . $image->getExtension();
                    $image->saveAs(News::images_folder() . $news->{$image_attr});
                }
            }
        }

        $news->save();

        if (!empty($this->tag_ids))
        {
            NewsTag::deleteAll(['news_id' => $news->id]);
            foreach ($this->tag_ids as $tag_id) {
                $news_tag = new NewsTag();
                $news_tag->tag_id = $tag_id;
                $news_tag->news_id = $news->id;
                $news_tag->save();
            }
        }

        return $news;
    }

}