The database schema is split into a collection of bzipped SQL files that will re-populate a database with our tables and data. These are roughtly split up based on the "types" of data we have in the database (restaurants and its associated joining tables for attributes and categories, for instance, are in restaurants.bz2).

The attribute/value pairs table was split into two compressed SQL files, due to the large size of the table. The first file (containing rows 1-114,185) also defines the table structure, while the second file (rows 114,186-228,371) just insert data into the table, which is assumed to already exist. Thus, you *must* execute these two files in order.

attr_values.bz2:
	attributes
	value_info

attribute_value_pairs_1-114185.bz2:
	attribute_value_pairs (table structure + rows 1-114185)

attribute_value_pairs_114186-228371.bz2:
	attribute_value_pairs (data for rows 114186-228371 only)

food_categories.bz2:
	categories
	foods
	
groups.bz2:
	groups
	group_members

raw_comments.bz2:
	raw_comments

raw_places.bz2:
	raw_places

raw_restaurants.bz2:
	raw_restaurants

restaurants.bz2:
	restaurants
	restaurant_attributes
	restaurant_categories

users.bz2:
	users
	user_pref_categories
	user_pref_foods