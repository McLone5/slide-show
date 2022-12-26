Feature: Photo folder, displaying other photo folders and photos

  Scenario:
    When I go to page "/test"
    Then I get a photo folder list
    And I get a photo list

    When I go to page "/test/sub-test"
    Then there's no photo folder list
    And I get a photo list
