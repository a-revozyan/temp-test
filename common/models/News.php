<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title_ru
 * @property string $title_uz
 * @property string $title_en
 * @property string $slug_uz
 * @property string $slug_ru
 * @property string $slug_en
 * @property string $image_ru
 * @property string $image_uz
 * @property string $image_en
 * @property string $short_info_ru
 * @property string $short_info_uz
 * @property string $short_info_en
 * @property string $body_ru
 * @property string $body_uz
 * @property string $body_en
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 * @property int $is_main
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $imageFileRu;
    public $imageFileUz;
    public $imageFileEn;

    public const STATUS = [
        'active' => 1,
        'inactive' => 0
    ];

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title_uz',
                'slugAttribute' => 'slug_uz',
                'ensureUnique' => true,
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title_ru',
                'slugAttribute' => 'slug_ru',
                'ensureUnique' => true,
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title_en',
                'slugAttribute' => 'slug_en',
                'ensureUnique' => true,
            ],
        ];
    }

    public static function images_folder(): string
    {
        return Yii::getAlias('@frontend') . '/web/uploads/cnews/';
    }

    public static function images_path(): string
    {
        return GeneralHelper::env('front_website_send_request_url') . '/uploads/cnews/';
    }

    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title_ru', 'title_uz', 'short_info_ru', 'short_info_uz', 'body_ru', 'body_uz', 'created_at', 'updated_at'], 'required'],
            [['body_ru', 'body_uz', 'body_en', 'created_at', 'updated_at'], 'string'],
            [['imageFileRu', 'imageFileUz', 'imageFileEn'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, PNG, JPG, JPEG'],
            [['status'], 'default', 'value' => null],
            [['is_main'], 'safe'],
            [['status'], 'integer'],
            [['title_ru', 'title_uz', 'title_en', 'short_info_ru', 'short_info_uz', 'short_info_en'], 'string', 'max' => 255],
        ];
    }

    public function uploadRu()
    {
        $this->imageFileRu->saveAs('../../frontend/web/uploads/cnews/' . $this->image_ru);
    }

    public function uploadUz()
    {
        $this->imageFileUz->saveAs('../../frontend/web/uploads/cnews/' . $this->image_uz);
    }

    public function uploadEn()
    {
        $this->imageFileEn->saveAs('../../frontend/web/uploads/cnews/' . $this->image_en);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title_ru' => Yii::t('app', 'Title Ru'),
            'title_uz' => Yii::t('app', 'Title Uz'),
            'title_en' => Yii::t('app', 'Title En'),
            'image_ru' => Yii::t('app', 'Image Ru'),
            'image_uz' => Yii::t('app', 'Image Uz'),
            'image_en' => Yii::t('app', 'Image En'),
            'short_info_ru' => Yii::t('app', 'Short Info Ru'),
            'short_info_uz' => Yii::t('app', 'Short Info Uz'),
            'short_info_en' => Yii::t('app', 'Short Info En'),
            'body_ru' => Yii::t('app', 'Body Ru'),
            'body_uz' => Yii::t('app', 'Body Uz'),
            'body_en' => Yii::t('app', 'Body En'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getTags()
    {
        return $this->hasMany(Tag::className(), ["id" => "tag_id"])->viaTable('news_tag', ['news_id' => "id"]);
    }

    public static function getFullClientArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullClientArr();
        }

        return $_models;
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'title_uz' => $this->title_uz,
            'title_ru' => $this->title_ru,
            'title_en' => $this->title_en,
            'slug_uz' => $this->slug_uz,
            'slug_ru' => $this->slug_ru,
            'slug_en' => $this->slug_en,
            'image_uz' => !empty($this->image_uz) ? News::images_path() . $this->image_uz : null,
            'image_ru' => !empty($this->image_ru) ? News::images_path() . $this->image_ru : null,
            'image_en' => !empty($this->image_en) ? News::images_path() . $this->image_en : null,
            'short_info_uz' => $this->short_info_uz,
            'short_info_ru' => $this->short_info_ru,
            'short_info_en' => $this->short_info_en,
            'body_uz' => $this->body_uz,
            'body_ru' => $this->body_ru,
            'body_en' => $this->body_en,
            'status' => $this->status,
            'is_main' => (int)$this->is_main,
            'created_at' => !empty($this->created_at) ? DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'updated_at' => !empty($this->updated_at) ? DateHelper::date_format($this->updated_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'tags' => $this->tags,
        ];
    }

    public function getFullClientArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'title' => $this->{"title_$lang"},
            'slug' => $this->{"slug_$lang"},
            'image' => !empty($this->{"image_$lang"}) ? News::images_path() . $this->{"image_$lang"} : null,
            'short_info' => $this->{"short_info_$lang"},
            'body' => $this->{"body_$lang"},
            'updated_at' => !empty($this->updated_at) ? DateHelper::date_format($this->updated_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'tags' => Tag::getFullClientArrCollection($this->tags),
        ];
    }
}
