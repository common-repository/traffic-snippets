=== Traffic Snippets === 
Author URI: https://lumnar.com
Plugin URI: https://lumnar.tech/
Donate link: https://lumnar.tech/
Contributors: maxlumnar
Tags: custom tracking, visitor insights, traffic analyzer, tracking snippets, traffic snippets, conversion tracker
Tested up to: 6.0
Requires PHP: 7.0
Stable tag: 1.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Traffic Snippets helps analyze the traffic in very clean and customized ways

== Description ==
Vertical traffic analysis based on flexible snippets and hooks. Extract the exact metrics you wish to analyse independently by injecting simple PHP or JS snippets. This allows you to analyse the relationship between the resulting data and conversion percentage or any target metrics by efficiently stripping the time vector.


== Frequently Asked Questions ==
= How do i know if the trend is downwards or upwards? =
Right now, in version 1.0 , there is no way to compare two campaigns, other than using the admin dashboard widget and comparing the numbers yourself. Duplicate a campaign, stop the running one, and run the second one. In future versions if there will be interest for such a feature, i will add such an option.
= Why would i use this instead of Google Analytics =
This is a developer tool, not an enduser feature. GA provides insane amount of data, but the simple and to the point stats are hard, if not impossible to obtain through GA. For example, If you need to know just how many visitors use metamask wallet, and what percentage of those place orders via cc payment option, you can find this out really fast and easy with this tool. Setting customized statistics based on user activity, or even running code based on their behaviour, its what makes this plugin so much different than any other tracking service.
= Do i need to know php/js to use this? =
Sort of yes. This is a developer tool. Basic use involves using just echo "varname"; but more advanced use requires better knowledge. Knowing what wordpress hooks to use is also important. 
= What does it mean vertical tracking =
The stats are saved per visitor and have relevance at any moment in time. For example, you care if a visitor bought something from the shop, not when.

== Installation ==
1. Go to `Plugins` in the Admin menu
2. Click on the button `Add new`
3. Search for `Traffic Snippets` and click 'Install Now' or click on the `upload` link to upload `traffic_snippets.zip`
4. Click on `Activate plugin`

== Changelog ==
= 1.0.1: June 6, 2022 =
* escaping and sanitizing variables, removed secondary php/js field, added FAQ
= 1.0.0: May 20, 2022 =
* Birthday of Traffic_Snippets