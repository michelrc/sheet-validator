Simple Sheets Validator
===============

A rule based (Excel) sheet validator. Initially created for payroll validation at
organizations using this kind of documents.

What it does
------------

Allows business expert to use rule repositories to express their validations and apply
them to sheets.

How it is done
--------------

There is a rule loader which `include` rules, based on [Ruler](https://github.com/bobthecow/Ruler)
the fetched files return a callable using a RuleBuilder as parameter,
the intention on this is to make the rules pluggable as much as possible.

Any feedback is welcome ;)

