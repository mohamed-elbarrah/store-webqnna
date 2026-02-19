<?php

if (!class_exists('qs')) {
	/**
	 * Quran Shan Main Class.
	 *
	 */
	class qs
	{
	
		//////////////////////////////////////////////////////////////////////////////////////////////
		function __construct(){
			// Create the query var for custom url
			add_filter( 'query_vars', array($this, 'userpage_rewrite_add_var'));
			add_action('init',array($this, 'userpage_rewrite_rule'));
			add_action( 'template_redirect', array($this, 'userpage_rewrite_catch'));
		}

		//////////////////////////////////////////////////////////////////////////////////////////////
		public static function _($text = '')
		{
			$locale = get_user_locale();
			$texts = array(
				'ltr' => array(
					'ar' => 'rtl',
				),
				'rtl' => array(
					'ar' => 'ltr',
				),
				'project options' => array(
					'ar' => 'خيارات المشاريع',
				),
				'project gifts' => array(
					'ar' => 'خيارات الإهداءات',
				),
				'is it project?' => array(
					'ar' => 'إضافة كمشروع؟',
				),
				'is it gift?' => array(
					'ar' => 'إضافة كإهداء؟',
				),
				'goal' => array(
					'ar' => 'الهدف',
				),
				'project' => array(
					'ar' => 'مشروع',
				),
				'projects' => array(
					'ar' => 'المشاريع',
				),
				'all projects' => array(
					'ar' => 'كل المشاريع',
				),
				'edit project' => array(
					'ar' => 'تعديل المشروع',
				),
				'update project' => array(
					'ar' => 'تحديث المشروع',
				),
				'search projects' => array(
					'ar' => 'بحث المشاريع',
				),
				'add new project' => array(
					'ar' => 'أضف مشروع جديد',
				),
				'parent project' => array(
					'ar' => 'المشروع الاب',
				),
				'separate projects with commas' => array(
					'ar' => 'أفصل المشاريع بفاصلة',
				),
				'add or remove projects' => array(
					'ar' => 'اضف او احذف المشاريع',
				),
				'choose from the most used projects' => array(
					'ar' => 'إختر من المشاريع الاكثر استخداما',
				),
				'stop collecting donations when the project is completed?' => array(
					'ar' => 'إيقاف جمع التبرعات عند إكتمال المشروع?',
				),
				'custom donate?' => array(
					'ar' => 'تبرع مخصص؟'
				),
				'add new' => array(
					'ar' => 'أضف جديد'
				),
				'name' => array(
					'ar' => 'الإسم'
				),
				'price' => array(
					'ar' => 'السعر'
				),
				'save' => array(
					'ar' => 'حفظ'
				),
				'cancel' => array(
					'ar' => 'الغاء'
				),
				'delete' => array(
					'ar' => 'حذف'
				),
				'share value' => array(
					'ar' => 'قيمة السهم'
				),
				'init value' => array(
					'ar' => 'القيمة الابتدائية'
				),
				'style' => array(
					'ar' => 'الشكل'
				)

			);

			if (isset($texts[strtolower($text)][$locale])) {
				return $texts[strtolower($text)][$locale];
			}
			return $text;
		}

		//////////////////////////////////////////////////////////////////////////////////////////////
		public static function add_custom_slug($slug, $dir)
		{
			global $custom_slugs;

			if (!isset($custom_slugs) || !is_array($custom_slugs))
				$custom_slugs = array();
			$custom_slugs[$slug] = $dir;
		}


		public static function custom_archives()
		{
			global $custom_slugs;
			if (!isset($custom_slugs) || !is_array($custom_slugs))
				$custom_slugs = array();
			return $custom_slugs;
		}

		public static function userpage_rewrite_add_var($vars)
		{
			foreach (self::custom_archives() as $slug => $dir) {
				$vars[] = $slug;
			}
			return $vars;
		}
		public static function userpage_rewrite_rule()
		{
			foreach (self::custom_archives() as $slug => $dir) {
				add_rewrite_tag('%' . $slug . '%', '([^&]+)');
				add_rewrite_rule(
					'^' . $slug . '/?',
					'index.php?messages=$matches[1]',
					'top'
				);
			}
		}
		public static function userpage_rewrite_catch()
		{
			global $wp, $wp_query, $current_user;

			$requests = explode('/', $wp->request);
			$request = $requests[0] ?? '';

			foreach (self::custom_archives() as $slug => $dir) {
				if (array_key_exists($slug, $wp_query->query_vars)) {
					include($dir);
					exit;
				} elseif ($request == $slug) {
					include($dir);
					exit;
				}
			}
		}
	}
}
new qs;