# Technical sheet

***Qui Paie Quoi*** (*Who Pays What*) is a web app to share its expenses with other Members and get the balance of who owes who and how much.

## Context

This is a training project for new apprentices at KNP Labs.

## Stack

### Environement

- **Docker + Docker-compose:** To manage needed tools easily and achieve fast deployment, for both dev and prod environment.
- **PHP:** Needed to run symfony apps.
- **MySQL:** We'll need to store data, but not much so a small and basic SQL relational database fit our needs.
- **Nginx:** Simple web server.
- **Make:** Used for useful commands to save time.
- **Adminer *(only dev)* :** Useful for viewing / debugging / changing data of the database during development.

### Back-end

- **Symfony:** We chose Symfony 7 for our fullstack web application as framework because it is maintainable, well structured and well-known by KNPeers.
- **Doctrine:** Links our app to the database, no matter what kind it is (MySQL or others).

### Front-end

- **Twig:** As a fullstack app, we'll use the basic tool provided by Symfony.
- **Bootstrap:** To implement the different parts of the views we'll use this framework which gives premade basic components.

## Architecture

```mermaid
flowchart
    subgraph ctr["Controller"]
            sym["Symfony"]
    end
    subgraph mdl["Model"]
            orm["Doctrine"]
            db["MySQL"]
    end
    subgraph vw["View"]
            front["Twig"]
    end
        cl["Client"] -- HTTP Rquest --> sym
        front -- HTML + CSS + JS --> cl
        orm -- Data ----> sym
        sym -- Request ----> orm
        db -- SQL Answer --> orm
        orm -- SQL Request --> db
        sym --> front

        style cl stroke:#D50000,fill:#FFCDD2
        style mdl stroke:#FF6D00,fill:#FFE0B2
        style ctr stroke:#AA00FF,fill:#E1BEE7
        style vw stroke:#00C853,fill:#C8E6C9
```
*Fullstack architecture*

```mermaid
classDiagram
    Group "1" o-- "*" Expense : containes
    Group "1" o-- "*" GroupMembership : has
    Expense "0" --> "1" ExpenseType : is
    GroupMembership "0" --> "1" GroupRole : is
    Participant "1" --o "*" Expense : pays
    Participant "1..*" --o "*" Expense : participates
    Member "1" o-- "*" GroupMembership : has
    Group "*" *--o "0" Debt : gives
    Group "1" *-- "1..*" Participant : isInvolved
    Participant "*" --o "0..1" Member : isLinked
    Member "1" --o "*" Expense : creates
    Debt "0" --> "*" Participant

    namespace Entities {
        class Member {
          -String email
          -String password
          -String[] roles
          +getUserIdentifier(): String
          +getEmail(): String
          +getName(): String
          +getGroups(): Group[]
          +addGroup(Group group)
          +removeGroup(Group group)
          +setEmail(String email)
          +setPassword(String hashedPassword)
          +setName(String name)
          +getExpenses(): Expense[] 
          +addExpense(Expense expense)
          +getParticipant(): Participant
          +eraseCredentials(): void
          +getInitials(): String
        }
        class GroupMembership {
            -Member member
            -GroupRole role
            -Group group
        }
        class Participant {
          -String name
          +getName(): String
          +getGroups(): Group[]
          +addGroup(Group group)
          +removeGroup(Group group)
          +setName(String name)
          +getExpenses(): Expense[] 
          +addExpense(Expense expense)
          +getMember(): ?Member
          +getInitials(): String
        }
        class Group {
          -String name
          -String code
          +getOwner(): Member
          +getBalance(): Debt[]
          +getParticipants(): Participant[]
          +addParticipant(Participant participant)
          +removeParticipant(Participant participant)
          +getExpenses(): Expense[]
          +addExpense(Expense expense)
          +removeExpense(Expense expense)
          +getCode(): String
          -generateCode(): String
        }
        class Expense {
          -String title
          -Text description
          -float amount
          -Date date
          +getTitle(): String
          +setTitle(Sting title)
          +getDescription(): Text
          +setDescription(Text description)
          +getAmount(): float
          +setAmount(float amount)
          +getDate(): Date
          +setDate(Date date)
          +getPayer(): Participant
          +setPayer(Participant participant)
          +getBeneficiaries(): Participant[]
          +addBeneficiary(Participant participant)
          +removeBeneficiary(Participant participant)
          +getCreator(): Member
          +getType(): ExpenseType
        }
    }
    class Debt{
        +Participant debtor
        +Participant creator
        +int amount
        +getParticipantAmount(): int
    }
    class GroupRole{
        <<enumeration>>
        OWNER
        EDITOR
        VIEWER
    }
    class ExpenseType{
        <<enumeration>>
        DRINK
        FOOD
        TRANSPORT
        ...
        OTHER
    }

```
*Class diagram*

## Test

For the moment, the tests are left out (seen with the team), but we'll use those:
- **PHPspec:** Unit tests
- **Behat:** Behavior Driven Development

## Risky beginnings

| Risks | Symfony (fullstack) |
| --- | :---: |
| Unstable | - |
| Need to learn, to acquire skill | - |
| Poorly documented | - | - |
| Not maintained anymore | - |
| Never used at KNP | - |
| Hard to test/lack test tooling | - |
| No KNPeers interested in learning it | - |
| Team members never worked together | + |
| Inexperienced devs | + |

- *- = no risk*
- *+ = risk*
- *++ = big risk*
- *+++ = too risky*
