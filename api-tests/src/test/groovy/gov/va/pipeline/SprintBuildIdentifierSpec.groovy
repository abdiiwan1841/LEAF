package gov.va.pipeline

import spock.lang.Specification
import spock.lang.Unroll

import java.time.*
import java.time.temporal.ChronoField
import java.time.temporal.ChronoUnit
import java.time.temporal.IsoFields

import static org.hamcrest.Matchers.*
import static org.hamcrest.MatcherAssert.assertThat
import static org.hamcrest.Matchers.equalTo


class SprintBuildIdentifierSpec extends Specification {


    static def SprintBuildIdentifier(ofDate = LocalDate.now())
    {
        def buildPrefix = "BLD"
        def buildSuffix = "XX"

        // Captures week number of the year
        int weekOfYear = ofDate.get(IsoFields.WEEK_OF_WEEK_BASED_YEAR)
        // last day of sprint are always on Tuesdays
        def lastDayOfSprint = ofDate.with(DayOfWeek.TUESDAY)

        // if week is ODD
        if(weekOfYear % 2 != 0) {
            // if day of the week is after Tuesday
            if(ofDate.getDayOfWeek().getValue() > 2) {
                // adds two weeks to the odd week to land the last sprint day on a tuesday
                lastDayOfSprint = lastDayOfSprint.plus(14, ChronoUnit.DAYS).with(DayOfWeek.TUESDAY)
            }
            // if day of the week is on or before Tuesday
            else { // if(ofDate.getDayOfWeek().getValue() <= 2)
                // last day of sprint is in the week of the date
                lastDayOfSprint = ofDate.with(DayOfWeek.TUESDAY)
            }
        }
        // if week is EVEN
        else { // if(weekOfYear % 2 == 0)
            // adds a week to the even week to land the last sprint day on a tuesday in the following odd week
            lastDayOfSprint = lastDayOfSprint.plus(7, ChronoUnit.DAYS).with(DayOfWeek.TUESDAY)
        }

        println("${buildPrefix}${lastDayOfSprint}${buildSuffix}")
        return "${buildPrefix}${lastDayOfSprint}${buildSuffix}".toString()
    }


    @Unroll
    def "unit tests"() {
        given:
        def paramDate = param.date
        when:
        def pipelineBuildIdentifier = SprintBuildIdentifier(paramDate)
        then:
        assertThat(pipelineBuildIdentifier, equalTo(expectedSprintBuildIdentifier))
        where:
        param                                     || expectedSprintBuildIdentifier
        [date: LocalDate.of(2020, 6, 17)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 18)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 19)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 20)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 21)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 22)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 23)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 24)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 25)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 26)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 27)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 28)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 29)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 6, 30)]         || "BLD2020-06-30XX"
        [date: LocalDate.of(2020, 7, 1)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 2)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 3)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 4)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 5)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 6)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 7)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 8)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 9)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 10)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 11)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 12)]          || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 13)]         || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 14)]         || "BLD2020-07-14XX"
        [date: LocalDate.of(2020, 7, 15)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 16)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 17)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 18)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 19)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 20)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 21)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 22)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 23)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 24)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 25)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 26)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 27)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 28)]         || "BLD2020-07-28XX"
        [date: LocalDate.of(2020, 7, 29)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 7, 30)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 7, 31)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 1)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 2)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 3)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 4)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 5)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 6)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 7)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 8)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 9)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 10)]         || "BLD2020-08-11XX"
        [date: LocalDate.of(2020, 8, 11)]         || "BLD2020-08-11XX"

    }




}

