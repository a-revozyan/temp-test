<?php
namespace backapi\models\forms\newsForms;

use common\models\News;
use common\models\NewsTag;
use common\models\Tag;
use yii\base\Model;
use yii\helpers\VarDumper;

class CreateNewsForm extends Model
{
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
            [['title_uz', 'title_ru', 'short_info_uz', 'short_info_ru',
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
        $image_uz = $this->image_uz;
        $image_ru = $this->image_ru;
        $image_en = $this->image_en;

        $news = new News();
        $news->setAttributes($this->attributes);
        $news->created_at = date('Y-m-d H:i:s');
        $news->updated_at = date('Y-m-d H:i:s');
        $news->save();

        if (!empty($image_uz))
        {
            $news->image_uz = $news->id ."news_uz_" . date('_Y_m_d_H_s_i') . "." . $image_uz->getExtension();
            $image_uz->saveAs(News::images_folder() . $news->image_uz);
        }
        if (!empty($image_ru))
        {
            $news->image_ru = $news->id ."news_ru_" . date('_Y_m_d_H_s_i') . "." . $image_ru->getExtension();
            $image_ru->saveAs(News::images_folder() . $news->image_ru);
        }
        if (!empty($image_en))
        {
            $news->image_en = $news->id ."news_en_" . date('_Y_m_d_H_s_i') . "." . $image_en->getExtension();
            $image_en->saveAs(News::images_folder() . $news->image_en);
        }

        $news->save();

        if (!empty($this->tag_ids))
        {
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