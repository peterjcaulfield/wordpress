Example code on how to register/create a widget area and then display a registered widget area.
This includes:
- [Registering a single widget area](#register-single)
- [Registering multiple widget areas](#register-multiple)
- [Displaying a widget area](#display)


#Creating widget areas

To create a widget area, you wrap a call to the wp function register_sidebar in a function declaration
that acts as the widget areas init. The register_sidebar wp function accepts an array which defines the
the params for the widget area you are looking to create. The init function is then bound to the 'widget_init'
hook 

This code goes in your functions.php file

<a name="register-single">
##Register a single widget area
```php
function theme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'domain' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
// add our widget registering function to the widgets_init event
add_action( 'widgets_init', 'theme_widgets_init' );
```
<name="register-multiple"/>
##Registering multiple widget areas in one function call
```php
function theme_widgets_init() {
	register_sidebars(3, array(
		'name'          => __( 'Footer Widget %d', 'domain' ),
		'id'            => 'footer-widget-%d',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
// add our widget registering function to the widgets_init event
add_action( 'widgets_init', 'theme_widgets_init' );
```
<name="display"/>
##Displaying a widget area

To display any registered widget area, you simply call the dynamic_sidebar
wp function with the id of the widget area you wish to display
This code goes in the php file of the page that is being rendered

```php
dynamic_sidebar('sidebar-1');
```

Adding widgets to a registered widget area

To add widgets to a registered widget area, from the admin dashboard navigate to Appearance -> Widgets






