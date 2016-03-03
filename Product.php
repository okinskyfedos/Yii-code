<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $alias
 * @property string $title
 * @property string $desc
 * @property string $article_factory
 * @property string $article_online
 * @property double $price_vendor
 * @property double $price_online
 * @property double $price_sales
 * @property string $shipping_days
 * @property integer $certified
 * @property string $design
 * @property integer $count_lamp
 * @property integer $count_lampshade
 * @property integer $transformer
 * @property integer $image
 * @property string $youtube_video
 * @property string $meta_title
 * @property string $meta_desc
 * @property string $meta_keys
 * @property string $updated
 * @property string $created
 * @property integer $delete
 * @property integer $subCategory_id
 * @property integer $brand_id
 *
 * @property ColorsHasProducts[] $colorsHasProducts
 * @property Colors[] $colors
 * @property Comments[] $comments
 * @property ComponentinstallationsHasProducts[] $componentinstallationsHasProducts
 * @property Componentinstallations[] $componentinstallations
 * @property ComponentlampsHasProducts[] $componentlampsHasProducts
 * @property Componentlamps[] $componentlamps
 * @property FactoryequipmentsHasProducts[] $factoryequipmentsHasProducts
 * @property Factoryequipments[] $factoryequipments
 * @property FormsHasProducts[] $formsHasProducts
 * @property Forms[] $forms
 * @property GroupsHasProducts[] $groupsHasProducts
 * @property Groups[] $groups
 * @property Images[] $images
 * @property LampshadecolorsHasProducts[] $lampshadecolorsHasProducts
 * @property Lampshadecolors[] $lampshadeColors
 * @property MaterialsHasProducts[] $materialsHasProducts
 * @property Materials[] $materials
 * @property Brands $brand
 * @property Subcategories $subCategory
 * @property StylesHasProducts[] $stylesHasProducts
 * @property Styles[] $styles
 * @property SubtypecategoriesHasProducts[] $subtypecategoriesHasProducts
 * @property Subtypecategories[] $subtypecategories
 * @property SuppliesHasProducts[] $suppliesHasProducts
 * @property Supplies[] $supplies
 * @property TypelampsHasProducts[] $typelampsHasProducts
 * @property Typelamps[] $typeLamps
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('NOW()'),

            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'title', 'desc', 'article_factory', 'article_online', 'price_vendor', 'price_online', 'price_sales', 'shipping_days', 'design', 'count_lamp', 'count_lampshade', 'image',  'subCategory_id', 'brand_id'], 'required'],
            [['desc'], 'string'],
            [['price_vendor', 'price_online', 'price_sales'], 'number'],
            [['certified', 'count_lamp', 'count_lampshade', 'transformer', 'image', 'delete', 'subCategory_id', 'brand_id'], 'integer'],
            [['updated', 'created'], 'safe'],
            [['alias', 'title', 'article_factory', 'article_online', 'shipping_days', 'design', 'youtube_video', 'meta_title', 'meta_desc', 'meta_keys'], 'string', 'max' => 255],
            [['alias'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'alias' => Yii::t('app', 'Alias'),
            'title' => Yii::t('app', 'Title'),
            'desc' => Yii::t('app', 'Desc'),
            'article_factory' => Yii::t('app', 'Article Factory'),
            'article_online' => Yii::t('app', 'Article Online'),
            'price_vendor' => Yii::t('app', 'Price Vendor'),
            'price_online' => Yii::t('app', 'Price Online'),
            'price_sales' => Yii::t('app', 'Price Sales'),
            'shipping_days' => Yii::t('app', 'Shipping Days'),
            'certified' => Yii::t('app', 'Certified'),
            'design' => Yii::t('app', 'Design'),
            'count_lamp' => Yii::t('app', 'Count Lamp'),
            'count_lampshade' => Yii::t('app', 'Count Lampshade'),
            'transformer' => Yii::t('app', 'Transformer'),
            'image' => Yii::t('app', 'Image'),
            'youtube_video' => Yii::t('app', 'Youtube Video'),
            'meta_title' => Yii::t('app', 'Meta Title'),
            'meta_desc' => Yii::t('app', 'Meta Desc'),
            'meta_keys' => Yii::t('app', 'Meta Keys'),
            'updated' => Yii::t('app', 'Updated'),
            'created' => Yii::t('app', 'Created'),
            'delete' => Yii::t('app', 'Delete'),
            'subCategory_id' => Yii::t('app', 'Sub Category ID'),
            'brand_id' => Yii::t('app', 'Brand ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorsHasProducts()
    {
        return $this->hasMany(ColorHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('colors_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComponentInstallationsHasProducts()
    {
        return $this->hasMany(ComponentInstallationHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComponentInstallations()
    {
        return $this->hasMany(ComponentInstallation::className(), ['id' => 'componentinstallation_id'])->viaTable('componentinstallations_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComponentLampsHasProducts()
    {
        return $this->hasMany(ComponentLampHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComponentLamps()
    {
        return $this->hasMany(ComponentLamp::className(), ['id' => 'componentlamp_id'])->viaTable('componentlamps_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactoryEquipmentsHasProducts()
    {
        return $this->hasMany(FactoryEquipmentHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactoryEquipments()
    {
        return $this->hasMany(FactoryEquipment::className(), ['id' => 'factoryequipment_id'])->viaTable('factoryequipments_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsHasProducts()
    {
        return $this->hasMany(FormHasProduct::className(), ['products_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForms()
    {
        return $this->hasMany(Form::className(), ['id' => 'forms_id'])->viaTable('forms_has_products', ['products_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupsHasProducts()
    {
        return $this->hasMany(GroupHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['id' => 'group_id'])->viaTable('groups_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLampshadeColorsHasProducts()
    {
        return $this->hasMany(LampShadeColorHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLampshadeColors()
    {
        return $this->hasMany(LampShadeColor::className(), ['id' => 'lampshadeColor_id'])->viaTable('lampshadecolors_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialsHasProducts()
    {
        return $this->hasMany(MaterialHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['id' => 'material_id'])->viaTable('materials_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategory()
    {
        return $this->hasOne(SubCategory::className(), ['id' => 'subCategory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStylesHasProducts()
    {
        return $this->hasMany(StyleHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStyles()
    {
        return $this->hasMany(Style::className(), ['id' => 'style_id'])->viaTable('styles_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubTypeCategoriesHasProducts()
    {
        return $this->hasMany(SubTypeCategoryHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubTypeCategories()
    {
        return $this->hasMany(SubTypecategory::className(), ['id' => 'subtypecategory_id'])->viaTable('subtypecategories_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuppliesHasProducts()
    {
        return $this->hasMany(SupplyHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplies()
    {
        return $this->hasMany(Supply::className(), ['id' => 'supplie_id'])->viaTable('supplies_has_products', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeLampsHasProducts()
    {
        return $this->hasMany(TypeLampHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeLamps()
    {
        return $this->hasMany(Typelamp::className(), ['id' => 'typeLamp_id'])->viaTable('typelamps_has_products', ['product_id' => 'id']);
    }
}
