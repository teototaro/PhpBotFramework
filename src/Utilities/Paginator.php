<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Utilities;

use PhpBotFramework\Entities\InlineKeyboard as Keyboard;

define("DELIMITER", "::::::::::::::::::::::::::::::::::::::\n");

/**
 * \class Paginator
 * \brief Delimitate items in one page.
 */
class Paginator
{
    /**
     * \addtogroup Utility-classes Utility classes
     * @{
     */

    /**
     * \brief Paginate a number of items got as a result by a query.
     * \details Take items to show in the page $index, delimiting in by $delimiter, and call the closure $format_item on each item paginated.
     * Taking a select query result, take items that have to be shown on page of index $index (calculated with $item_per_page).
     * @param mixed $items Result of a select query using pdo object.
     * @param int $index Index of the page to show.
     * @param PhpBotFramework\Entities\InlineKeyboard $keyboard Inline keyboard object to call PhpBotFramework\\Entities\\InlineKeyboard::addListKeyboard() on it for browsing results.
     * @param closure $format_item A closure that take the item to paginate and the keyboard. You can use it to format the item and add some inline keyboard button for each item.
     * @param string $prefix Prefix to pass to InlineKeyboard::addListKeyboard.
     * @param string $delimiter A string that will be used to concatenate each item.
     * @return string The string message with the items of page $index, with $delimiter between each of them.
     * @see PhpBotFramework\\Entities\\InlineKeyboard
     */
    public static function paginateItems(
        $items,
        int $index,
        Keyboard &$keyboard,
        callable $format_item,
        int $item_per_page = 3,
        string $prefix = 'list',
        string $delimiter = DELIMITER
    ) : string {
        // Assign the position of first item to show
        $item_position = ($index - 1) * $item_per_page + 1;

        $items_number = $items->rowCount();

        $counter = 1;

        $items_displayed = 0;

        $total_pages = intval($items_number / $item_per_page);

        // If there an incomplete page
        if (($items_number % $item_per_page) != 0) {
            $total_pages++;
        }

        // Initialize keyboard with the list
        $keyboard->addListKeyboard($index, $total_pages, $prefix);

        $message = '';

        // Iterate over all results
        while ($item = $items->fetch()) {
            // If we have to display the first item of the page and we
            // found the item to show (using the position calculated before)
            if ($items_displayed === 0 && $counter === $item_position) {
                $message .= $format_item($item, $keyboard);
                $items_displayed++;
            // If there is space for other items
            } elseif ($items_displayed > 0 && $items_displayed < $item_per_page) {
                $message .= $delimiter;
                $message .= $format_item($item, $keyboard);

                $items_displayed++;
            } elseif ($items_displayed === $item_per_page) {
                break;
            } else {
                $counter++;
            }
        }

        return $message;
    }
}

/*
 * @}
 */
