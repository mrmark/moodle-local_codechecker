<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Checks that various tokens have required PHPDoc block.
 *
 * @package   local_codechecker
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_Sniffs_Commenting_RequiredCommentSniff implements PHP_CodeSniffer_Sniff {

    public function register() {
        return array(
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
            T_PROPERTY,
            T_CONST,
            T_STRING,
        );
    }

    public function process(PHP_CodeSniffer_File $file, $stackptr) {
        $tokens = $file->getTokens();

        if ($tokens[$stackptr]['code'] === T_STRING && $tokens[$stackptr]['content'] !== 'define') {
            return; // Ignore all other T_STRING.
        }

        $prevToken = $file->findNext(T_WHITESPACE, $stackptr - 1, null, true);

        if ($prevToken === false) {
            $t
        }

        if ($tokens[$prevToken]['code'] !== T_OPEN_TAG) {

        }

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $stackptr - 1);
        if ($prevopentag !== false) {
            return;
        }



        // Find the end of the first PHPDoc block.
        $nextToken = $file->findNext(T_DOC_COMMENT_CLOSE_TAG, $stackptr);

        if ($nextToken === false) {
            // Did not find any doc blocks, so must be missing.
            $file->addError('File-level PHPDoc block is not found', $stackptr, 'FileComment');
        }

        // Find prior token.
        $prevToken = $file->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $nextToken, null, true);
        if ($tokens[$prevToken]['code'] !== T_OPEN_TAG) {
            // Not positioned at the top of the file, so must not be the file PHPDoc block.
            $file->addError('File-level PHPDoc block is not found', $stackptr, 'FileComment');
        }

        // Find the next non-whitespace token.
        $nextToken = $file->findNext(T_WHITESPACE, $nextToken + 1, null, true);

        if ($nextToken === false) {
            return; // Nothing else follows, assume it is OK.
        }

        // These are tokens that can have PHPDocs.
        $canHaveDocs = array(
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
            T_CLOSURE,
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_FINAL,
            T_STATIC,
            T_ABSTRACT,
            T_CONST,
            T_PROPERTY,
            T_OBJECT,
        );

        if (in_array($tokens[$nextToken]['code'], $canHaveDocs) === true) {
            // The doc block that we found belongs to a class, function, etc and not to the file.
            $file->addError('File-level PHPDoc block is not found', $nextToken, 'FileComment');
        }

        // Allow phpdoc before define() token (see CONTRIB-4150).
        if ($tokens[$nextToken]['code'] == T_STRING && $tokens[$nextToken]['content'] == 'define') {
            // The doc block that we found belongs to a define statement and not to the file.
            $file->addError('File-level PHPDoc block is not found', $nextToken, 'FileComment');
        }
    }
}
